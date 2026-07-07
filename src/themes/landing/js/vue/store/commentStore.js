const commentStore = new Vuex.Store({
    state: {
        commentData: [],
        loading: false,
        error: null,
        loadedComments: false,
        quoteId: '',
        quoteAccount: '',
        modelId: '',
        modelUuid: '',
        modelRef: '',
        modelType: '',
    },
    
    mutations: {
        SET_LOADING(state, loading) {
            state.loading = loading;
        },
        SET_COMMENT_DATA(state, payload) {
            state.commentData = Array.isArray(payload) ? payload : [];
        },
        ADD_COMMENT(state, payload) {
            if (!payload || typeof payload !== 'object') {
                return;
            }

            const comment = { ...payload };
            if (!comment.user_name) {
                comment.user_name = comment.author || 'Unknown User';
            }

            const currentComments = Array.isArray(state.commentData) ? state.commentData : [];
            state.commentData = [comment, ...currentComments];
        },
        ADD_REPLY_TO_PARENT(state, payload) {
            if (!payload || typeof payload !== 'object') {
                return;
            }

            const reply = { ...payload };
            if (!reply.user_name) {
                reply.user_name = reply.author || 'Unknown User';
            }

            const parentCommentId = reply.parent_id !== undefined && reply.parent_id !== null
                ? Number(reply.parent_id)
                : null;

            const currentComments = Array.isArray(state.commentData) ? state.commentData : [];
            state.commentData = currentComments.map((comment) => {
                if (parentCommentId === null || Number(comment.comment_id) !== parentCommentId) {
                    return comment;
                }

                const existingReplies = Array.isArray(comment.replay)
                    ? comment.replay
                    : (Array.isArray(comment.reply) ? comment.reply : []);

                return {
                    ...comment,
                    replay: [...existingReplies, reply],
                };
            });
        },
        SET_ERROR(state, error) {
            state.error = error;
        },
        SET_QUOTE_ID(state, quoteId) {
            state.quoteId = quoteId || '';
        },
        SET_QUOTE_ACCOUNT(state, quoteAccount) {
            state.quoteAccount = quoteAccount || '';
        },
        SET_MODEL_ID(state, modelId) {
            state.modelId = modelId || '';
        },
        SET_MODEL_UUID(state, modelUuid) {
            state.modelUuid = modelUuid || '';
        },
        SET_MODEL_REF(state, modelRef) {
            state.modelRef = modelRef || '';
        },
        SET_MODEL_TYPE(state, modelType) {
            state.modelType = modelType || '';
        },
    },
    
    actions: {
        async loadComments({ commit }, payload = {}) {
            commit('SET_LOADING', true);
            commit('SET_ERROR', null);
            // old state for quote
            commit('SET_QUOTE_ID', payload.quoteId);
            commit('SET_QUOTE_ACCOUNT', payload.quoteAccount);
            // new state for model
            commit('SET_MODEL_ID', payload.model_id);
            commit('SET_MODEL_UUID', payload.model_uuid);
            commit('SET_MODEL_REF', payload.model_ref);
            commit('SET_MODEL_TYPE', payload.model_type);
        
            try {
                const svc = await import('../services/commentService.js');
                const res = await svc.default.loadComments(payload);
                console.log('loadComments res=', res);
                if(res && res.status === 404){
                    commit('SET_COMMENT_DATA', [{},{}]);
                    commit('SET_LOADING', false);
                    return;
                }
                commit('SET_COMMENT_DATA', res);
                commit('SET_LOADING', false);
            } catch (error) {
                console.error('Error loading comments:', error);
                commit('SET_ERROR', error.message || 'Failed to load comments');
                commit('SET_LOADING', false);
                throw error;
            }
        },    

        async submitComment({ commit, state }, formData) {
            commit('SET_LOADING', true);
            commit('SET_ERROR', null);

            
            try {
                const svc = await import('../services/commentService.js');
                const res = await svc.default.submitComment(formData);
                if (res && res.comment) {
                    commit('ADD_COMMENT', res.comment);
                }
                commit('SET_LOADING', false);


            } catch (error) {
                console.error('Failed to submit comment:', error);
                commit('SET_ERROR', error.message || 'Failed to submit comment');
                commit('SET_LOADING', false);
                throw error;
            } 
        },

        async deleteComment({ commit,state }, commentId) {
            commit('SET_LOADING', true);
            commit('SET_ERROR', null);

            try {
                const svc = await import('../services/commentService.js');
                const res = await svc.default.deleteComment(commentId);

                const currentComments = Array.isArray(state.commentData) ? state.commentData : [];
                const newCommentData = currentComments.filter(comment => comment.comment_id !== commentId);

                commit('SET_COMMENT_DATA', newCommentData);
                commit('SET_LOADING', false);
            } catch (error) {
                console.error('Failed to delete comment:', error);
                commit('SET_ERROR', error.message || 'Failed to delete comment');
                commit('SET_LOADING', false);
                throw error;
            }
        },

        async upvoteComment({ commit, state }, payload) {
            const { commentId, user_id, uuid } = payload;
            commit('SET_LOADING', true);
            commit('SET_ERROR', null);

            try {
                const svc = await import('../services/commentService.js');
                const res = await svc.default.upvoteComment(commentId, user_id, uuid);

                // The API now returns { comment_id, votes, liked } so we use server
                // values as the source of truth instead of guessing client-side.
                const nextVotes = (res && typeof res.votes === 'number') ? res.votes : null;
                const nextLiked = (res && typeof res.liked === 'boolean') ? res.liked : null;

                const applyToComment = (comment) => {
                    if (!comment || Number(comment.comment_id) !== Number(commentId)) {
                        return comment;
                    }
                    return {
                        ...comment,
                        votes: nextVotes !== null ? nextVotes : (Number(comment.votes) || 0),
                        liked: nextLiked !== null ? nextLiked : Boolean(comment.liked),
                    };
                };

                const currentComments = Array.isArray(state.commentData) ? state.commentData : [];
                const newCommentData = currentComments.map((comment) => {
                    const updated = applyToComment(comment);
                    const replies = Array.isArray(updated.replay) ? updated.replay : [];
                    if (replies.length === 0) {
                        return updated;
                    }
                    return {
                        ...updated,
                        replay: replies.map(applyToComment),
                    };
                });

                commit('SET_COMMENT_DATA', newCommentData);
                commit('SET_LOADING', false);
            } catch (error) {
                console.error('Failed to upvote comment:', error);
                commit('SET_ERROR', error.message || 'Failed to upvote comment');
                commit('SET_LOADING', false);
                throw error;
            }
        },
        async replyCommentSubmit({ commit,state }, payload) {
            const { commentId, user_id, replyContent, is_reply } = payload;
            commit('SET_LOADING', true);
            commit('SET_ERROR', null);

            const formData = new FormData();
            formData.append('content', replyContent);
            formData.append('comment_id', commentId);
            formData.append('user_id', user_id);
            formData.append('is_reply', is_reply);
            try {
                const svc = await import('../services/commentService.js');
                const res = await svc.default.submitReplyComment(formData);

                if (res && res.comment) {
                    const replyPayload = {
                        ...res.comment,
                        parent_id: res.comment.parent_id !== undefined && res.comment.parent_id !== null
                            ? res.comment.parent_id
                            : commentId,
                    };
                    commit('ADD_REPLY_TO_PARENT', replyPayload);
                }
                commit('SET_LOADING', false);
            } catch (error) {
                console.error('Failed to reply comment:', error);
                commit('SET_ERROR', error.message || 'Failed to reply comment');
                commit('SET_LOADING', false);
                throw error;
            }
        },
    },
    
    getters: {
        loading: state => state.loading,
        error: state => state.error,
        loadedComments: state => state.loadedComments,
        commentData: state => state.commentData,
        quoteId: state => state.quoteId,
        quoteAccount: state => state.quoteAccount,
        modelId: state => state.modelId,
        modelUuid: state => state.modelUuid,
        modelRef: state => state.modelRef,
        modelType: state => state.modelType,
    }
});

export default commentStore;

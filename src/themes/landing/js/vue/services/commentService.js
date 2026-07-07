class CommentService {

    
    constructor() {
        this.baseURL = '/api/comments';
    }
    async loadComments(payload = {}, demoData = true) {
        try {
            // const response = demoData ? CommentService.demoData : await fetch(`${this.baseURL}`, {
            //     method: 'POST',
            //     body: JSON.stringify(payload),
            // });
            // const response = await fetch(`${this.baseURL}/${payload.model_id}?model_type=${payload.model_type}`, {
            const response = await fetch(`${this.baseURL}/${payload.model_uuid}?model_type=${payload.model_type}`, {
                method: 'GET',
            });
            if (!response.ok && !demoData) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error loading searchbar:', error);
            throw error;
        }
    }
    async getCommentData(commentData) {
        // return CommentService.demoData;
        try {
            // /api/global-search?query=Kenni
            const response = await fetch(`${this.baseURL}?query=${searchValue}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                },
            });
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error getting search results:', error);
            throw error;
        }
    }

    async submitComment(formData) {
        try {
            const response = await fetch(`${this.baseURL}/save`, {
                method: 'POST',
                body: formData
            })
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            return data;

        } catch (error) {

        }
    }

    async deleteComment(commentId) {
        console.log('deleteComment commentId=', commentId);
        try {
            const response = await fetch(`${this.baseURL}/${commentId}`, {
                method: 'DELETE',
            });
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error deleting comment:', error);
            throw error;
        }
    }

    async upvoteComment(commentId, user_id, uuid) {
        console.log('upvoteComment commentId=', commentId, 'user_id=', user_id);
        try {
            const response = await fetch(`${this.baseURL}/upvote`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ comment_id: commentId, user_id: user_id, uuid: uuid }),
            });
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error upvoting comment:', error);
            throw error;
        }
    }

    async submitReplyComment(formData) {
        try {
            const response = await fetch(`${this.baseURL}/reply`, {
                method: 'POST',
                body: formData
            });
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            return data;
            // console.log('datassssss', data);
        } catch (error) {
            console.error('Error submitting reply comment:', error);
            throw error;
        }
    }
}

const commentService = new CommentService();
export default commentService;
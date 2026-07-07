const showroomStore = new Vuex.Store({
    state: {
        selectedSection: null,
        sections: [],
        selectedSectionData: [],
        loading: false,
        error: null
    },
    
    mutations: {
        SET_LOADING(state, loading) {
            state.loading = loading;
        },
        
        SET_ERROR(state, error) {
            state.error = error;
        },
        
        SET_SELECTED_SECTION(state, section) {
            state.selectedSection = section;
        },
        
        SET_SECTIONS(state, sections) {
            state.sections = sections;
        },
        
        CLEAR_SELECTED_SECTION(state) {
            state.selectedSection = null;
        }
    },
    
    actions: {
        async setSelectedSection({ commit }, sectionData) {
            commit('SET_LOADING', true);
            commit('SET_ERROR', null);
            
            try {
                commit('SET_SELECTED_SECTION', sectionData);
            } catch (error) {
                commit('SET_ERROR', error.message);
            } finally {
                commit('SET_LOADING', false);
            }
        },
        
        async loadSections({ commit }) {
            commit('SET_LOADING', true);
            commit('SET_ERROR', null);
            
            try {
                // This would typically fetch from an API
                const sections = [
                    { id: 1, name: 'Collaborative Hub', slug: 'collaborative-hub' },
                    { id: 2, name: 'Work Hub', slug: 'work-hub' },
                    { id: 3, name: 'Design Library', slug: 'design-library' },
                    { id: 4, name: 'Adaptive Solutions', slug: 'adaptive-solutions' },
                    { id: 5, name: 'Chair Display', slug: 'chair-display' },
                    { id: 6, name: 'Conference', slug: 'conference' },
                    { id: 7, name: 'Hospitality Hub', slug: 'hospitality-hub' },
                    { id: 8, name: 'Cafe', slug: 'cafe' },
                    { id: 9, name: 'Behavioral Health Display Wall', slug: 'behavioral-health-display-wall' },
                    { id: 10, name: 'Exam Spaces Room 1', slug: 'exam-spaces-room-1' },
                    { id: 11, name: 'Exam Spaces Room 2', slug: 'exam-spaces-room-2' },
                    { id: 12, name: 'In-between Spaces', slug: 'in-between-spaces' }
                ];
                
                commit('SET_SECTIONS', sections);
            } catch (error) {
                commit('SET_ERROR', error.message);
            } finally {
                commit('SET_LOADING', false);
            }
        },
        
        clearSelectedSection({ commit }) {
            commit('CLEAR_SELECTED_SECTION');
        }
    },
    
    getters: {
        selectedSection: state => state.selectedSection,
        sections: state => state.sections,
        loading: state => state.loading,
        error: state => state.error,
        
        getSectionBySlug: state => slug => {
            return state.sections.find(section => section.slug === slug);
        }
    }
});

export default showroomStore;

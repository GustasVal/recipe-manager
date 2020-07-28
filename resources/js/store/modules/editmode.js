import LocalStorage from "../../modules/LocalStorage";

const state = () => ({
    data: { enabled: false, editing: false, ...LocalStorage.get('edit-mode') || {} }
});

const mutations = {
    enable(state, { enable }) {
        if (!Laravel.isLoggedIn) {
            enable = false;
        }

        if (!enable) {
            state.data.editing = false;
        }

        state.data.enabled = enable;
        LocalStorage.update("edit-mode", "enabled", enable);
    },
    edit(state, { editing }) {
        if (!state.data.enabled) {
            return;
        }

        state.data.editing = editing;
        LocalStorage.update("edit-mode", "editing", editing);
    }
}

export default {
    namespaced: true,
    state,
    mutations
}

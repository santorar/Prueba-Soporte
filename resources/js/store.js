import Vue from 'vue';
import Vuex from 'vuex';
import axios from 'axios'; // Asegúrate de tener axios instalado

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        tasks: [] // Estado inicial para las tareas
    },
    mutations: {
        FETCH_TASKS(state, tasks){
            state.tasks = tasks;
        },
        ADD_TASK(state, {task, filter}) {
            if(filter !== "completed")
                state.tasks.push(task);
        },
        COMPLETE_TASK(state, {completedTask, filter}){
            if(filter !== "pending"){
                const index = state.tasks.findIndex(t => t.id === completedTask.id);
                if (index !== -1){
                    Vue.set(state.tasks, index, completedTask)
                }
            }else{
                state.tasks = state.tasks.filter(t => t.id !== completedTask.id);
            }
        },
        UPDATE_TASK(state, updatedTask) {
            const index = state.tasks.findIndex(t => t.id === updatedTask.id);
            if (index !== -1) {
                Vue.set(state.tasks, index, updatedTask);
            }
        },
        DELETE_TASK(state, taskId) {
            state.tasks = state.tasks.filter(t => t.id !== taskId);
        }
    },
    actions: {
        fetchTasks({ commit }, payload){
            axios.get(`/tasks`,payload)
                .then(response => {
                    commit('FETCH_TASKS', response.data)
                })
                .catch(error => {
                    console.error("Error getting tasks:", error);
                });
        },
        addTask({ commit }, {task, filter}) {
            axios.post('/tasks', task)
                .then(response => {
                    task = response.data;
                    commit('ADD_TASK', {task, filter});
                })
                .catch(error => {
                    console.error("Error adding task:", error);
                });
        },
        completeTask({ commit }, {taskId, filter}){
            axios.post(`/tasks-complete/${taskId}`)
                .then(response => {
                    commit('COMPLETE_TASK',{completedTask: response.data, filter: filter});
                })
                .catch(error => {
                    console.error("Error completing task:", error);
                });
        },
        updateTask({ commit }, task) {
            axios.put(`/tasks/${task.id}`, task)
                .then(response => {
                    commit('UPDATE_TASK', response.data);
                })
                .catch(error => {
                    console.error("Error updating task:", error);
                });
        },
        deleteTask({ commit }, taskId) {
            axios.delete(`/tasks/${taskId}`)
                .then(() => {
                    commit('DELETE_TASK', taskId);
                })
                .catch(error => {
                    console.error("Error deleting task:", error);
                });
        }
    },
    getters: {
        tasks: state => state.tasks
    }
});

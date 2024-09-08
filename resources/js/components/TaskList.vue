<template>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Task List</h1>
        <div class="d-flex justify-content-end align-items-center mb-2">
            <p class="mb-1 mr-2">Filter:</p>
            <select class="mb-1" v-model="selectedFilter">
                <option v-for="option in optionFilters" :value="option.value">
                    {{ option.text }}
                </option>
            </select>
        </div>
        <div v-show="loading" class="spinner-border spinner-border-sm" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <ul class="list-group mb-4" v-show="!loading">
            <li v-for="task in tasks" :key="task.id" class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">{{ task.title }}</h5>
                    <p class="mb-1">{{ task.description }}</p>
                    <small class="text-muted">Assigned to: {{ task.user }}</small>
                </div>
                <div>
                    <button :disabled=!!task.completed class="btn btn-success btn-sm mr-2" @click="completeTask(task.id)">Complete</button>
                    <button class="btn btn-danger btn-sm" @click="deleteTask(task.id)">Delete</button>
                </div>
            </li>
        </ul>
        <form @submit.prevent="addTask" class="card card-body">
            <div class="form-group">
                <input v-model="newTask.title" class="form-control" placeholder="Task Title" required>
            </div>
            <div class="form-group">
                <input v-model="newTask.description" class="form-control" placeholder="Task Description" required>
            </div>
            <div class="form-group">
                <input v-model="newTask.user" class="form-control" placeholder="Assigned User" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Add Task</button>
        </form>
    </div>
</template>

<script>
import { mapState, mapActions, mapGetters } from 'vuex';

export default {
    data() {
        return {
            newTask: {
                title: '',
                description: '',
                user: ''
            },
            selectedFilter: null,
            loading: false,
            optionFilters: [
                { value: null, text: "All"},
                { value: "pending", text: "Pending"},
                { value: "completed", text: "Completed"}
            ]
        };
    },
    computed: {
        ...mapState(['tasks']), // Simplificado para mapState
    },
    methods: {
        ...mapActions(['fetchTasks', 'addTask', 'completeTask', 'deleteTask']),
        addTask() {
            if (!this.newTask.title || !this.newTask.description || !this.newTask.user) {
                alert('Both title and description are required');
                return;
            }
            // Se utiliza la acción 'addTask' y luego se limpia el formulario
            this.$store.dispatch('addTask', {task: this.newTask, filter: this.selectedFilter}).then(() => {
                this.newTask.title = '';
                this.newTask.description = '';
                this.newTask.user = '';
            }).catch(error => {
                console.error('Error adding task:', error);
            });
        },
        completeTask(taskId) {
            // Se utiliza la acción 'completeTask'
            this.$store.dispatch('completeTask', {taskId: taskId, filter: this.selectedFilter}).then(()=>{
            }).catch(error => {
                console.error('Error completing task:', error);
            });
        },
        deleteTask(taskId) {
            // Se utiliza la acción 'deleteTask'
            this.$store.dispatch('deleteTask', taskId).catch(error => {
                console.error('Error deleting task:', error);
            });
        },
        refreshTasks() {
            // Actualiza las tareas usando la acción 'fetchTasks'
            this.loading = true;
            this.$store.dispatch('fetchTasks',{params:{ filter: this.selectedFilter}}).catch(error => {
                console.error("Error refreshing tasks:", error);
            });
            this.loading = false;
        }
    },
    watch: {
        // Actualiza las tareas cuando se cambia el filtro
        selectedFilter() {
            this.refreshTasks();
        }
    },
    mounted() {
        // Actualiza las tareas al montar el componente
        this.refreshTasks();
    }
};
</script>

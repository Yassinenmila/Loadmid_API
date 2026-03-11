<template>
  <div>
    <h1>Message depuis JSONPlaceholder :</h1>
    <p class="text-blue-700">{{ todo.title }}</p>
    <button @click="fetchTodo">Rafraîchir</button>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import axios from 'axios'

export default {
  setup() {
    const todo = ref({ title: '' }) // variable réactive

    const fetchTodo = async () => {
      try {
        const response = await axios.get('https://jsonplaceholder.typicode.com/todos/4')
        todo.value = response.data
      } catch (error) {
        console.error('Erreur API:', error)
        todo.value = { title: 'Impossible de récupérer les données' }
      }
    }

    onMounted(() => {
      fetchTodo() // récupère les données au montage
    })

    return { todo, fetchTodo }
  }
}
</script>

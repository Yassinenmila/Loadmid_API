<template>
  <div>
    <h1>Message depuis Laravel :</h1>
    <p>{{ message }}</p>
    <button @click="fetchHello">Rafraîchir</button>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { getHello } from '../services/api'

export default {
  setup() {
    const message = ref('') // variable réactive pour stocker le message

    // Fonction pour récupérer le message depuis l'API
    const fetchHello = async () => {
      try {
        const response = await getHello()
        message.value = response.data.message // récupère le message
      } catch (error) {
        console.error('Erreur API:', error)
        message.value = 'Impossible de récupérer le message'
      }
    }

    onMounted(() => {
      fetchHello() // récupère le message dès le montage du composant
    })

    return { message, fetchHello }
  }
}
</script>

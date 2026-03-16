<template>
  <div class="bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md transform transition-all hover:scale-[1.01]">

      <div class="text-center mb-10">
        <h1 class="text-3xl font-bold text-gray-800">Bienvenue</h1>
        <p class="text-gray-500 mt-2">Heureux de vous revoir !</p>
      </div>

      <form @submit.prevent="handleLogin" class="space-y-6">

        <!-- Erreur email -->
        <div v-if="error.email" class="bg-red-100 text-red-700 p-3 rounded-lg text-sm">
          {{ error.email }}
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Adresse Email</label>
          <input v-model="email" type="email" placeholder="nom@exemple.com"
            class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:border-indigo-500 focus:bg-white focus:outline-none transition duration-200"
            required
          >
        </div>

        <!-- Mot de passe -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
          <input v-model="password" type="password" placeholder="••••••••"
            class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:border-indigo-500 focus:bg-white focus:outline-none transition duration-200"
            required
          >
          <div class="text-right mt-2">
            <a href="#" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">Mot de passe oublié ?</a>
          </div>

          <!-- Erreur password -->
          <div v-if="error.password" class="bg-red-100 text-red-700 p-3 rounded-lg text-sm mt-2">
            {{ error.password }}
          </div>
        </div>

        <!-- Bouton -->
        <button type="submit"
          class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-lg shadow-lg hover:shadow-xl transition duration-300 transform active:scale-95">
          Se connecter
        </button>
      </form>

      <p class="text-center text-sm text-gray-600 mt-10">
        Pas encore de compte ?
        <router-link to="/signup" class="font-bold text-indigo-600 hover:underline">S'inscrire</router-link>
      </p>

    </div>
  </div>
</template>

<script>
import { ref } from 'vue'
import { login } from '../services/auth'
import { useRouter } from 'vue-router'

export default {
  setup() {
    const email = ref('')
    const password = ref('')
    const error = ref({})

    const router = useRouter()

    const handleLogin = async () => {
      error.value = {} // reset errors
      try {
        const res = await login({ email: email.value, password: password.value })
        localStorage.setItem('token', res.data.token) // stock le token
        router.push('/hello') // redirige vers la page protégée
      } catch (err) {
        // Affiche les erreurs
        if (err.response && err.response.data) {
          error.value = err.response.data.errors || { email: err.response.data.message }
        } else {
          error.value = { email: 'Erreur serveur' }
        }
      }
    }

    return { email, password, handleLogin, error, password }
  }
}
</script>

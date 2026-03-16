<template>
  <div class="bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 min-h-screen flex items-center justify-center p-4">

    <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md">

      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Créer un compte</h1>
        <p class="text-gray-500 mt-2">Rejoignez l'aventure en quelques clics.</p>
      </div>

    <form @submit.prevent="handleRegister" class="space-y-4">

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nom complet</label>
        <input v-model="form.name" type="text" placeholder="Jean Dupont" class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:border-purple-500 focus:bg-white focus:outline-none"/>
      </div>

<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Localisation</label>
<input v-model="form.location" type="text" placeholder="Location"
class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:border-purple-500 focus:bg-white focus:outline-none"/>
</div>

<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Adresse Email</label>
<input v-model="form.email" type="email" placeholder="nom@exemple.com"
class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:border-purple-500 focus:bg-white focus:outline-none"/>
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
<input v-model="form.password" type="password" placeholder="••••••••"
class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:border-purple-500 focus:bg-white focus:outline-none"/>
</div>

<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Confirmer</label>
<input v-model="form.password_confirmation" type="password" placeholder="••••••••"
class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:border-purple-500 focus:bg-white focus:outline-none"/>
</div>

</div>

<button
type="submit"
class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold py-3 rounded-lg shadow-lg mt-4"
>
S'inscrire
</button>

</form>

<p class="text-center text-sm text-gray-600 mt-8">
Déjà un compte ?
<router-link to="/login" class="font-bold text-purple-600 hover:underline">
Se connecter
</router-link>
</p>

</div>
</div>
</template>

<script setup>

import { ref } from 'vue'
import { register } from '../services/auth'
import { useRouter } from 'vue-router'

const router = useRouter()

const form = ref({
name: '',
location: '',
email: '',
password: '',
password_confirmation: ''
})

const handleRegister = async () => {

try {

await register(form.value)

router.push('/login')

} catch (error) {
console.log(error.response.data)
}

}

</script>

<template>
  <div class="social-login">
    <GoogleLogin :callback="loginGoogle" />
  </div>
</template>

<script setup>
import axios from 'axios';
import { decodeCredential } from 'vue3-google-login';

const emit = defineEmits(['login:success']);
// Função callback para o login com o Google
const loginGoogle = (response) => {
  // Verifica se o objeto retornado possui o método getBasicProfile
  const googleUser = decodeCredential(response.credential);
  console.log('google Login response:', response);
  console.log(googleUser);
  if (googleUser) {
    // const profile = googleUser.getBasicProfile();
    // const userInfo = {
    //   id: profile.getId(),
    //   name: profile.getName(),
    //   imageUrl: profile.getImageUrl(),
    //   email: profile.getEmail(),
    // };
    console.log('Informações do usuário:', googleUser);

    // Exemplo: enviar o token de ID para o backend para validação
    axios
      .post('/auth/google/callback', {
        token: response.credential,
      })
      .then((response) => {
        // Processar resposta do servidor
        console.log('Resposta do backend:', response.data);
        emit('login:success', response.data);
      })
      .catch((error) => {
        console.error('Erro ao autenticar no backend:', error);
      });
  } else {
    console.error(
      'O objeto googleUser não contém as informações do perfil. Verifique a integração do Google Login.',
    );
  }
};
</script>

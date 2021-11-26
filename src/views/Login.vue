<template>
  <v-container>
    <v-card width="500" max-width="96%" class="mx-auto">
      <v-form ref="form-login" @submit.prevent="submitLoginForm" :disabled="loading">
        <v-card-text>
          <v-text-field
            label="Usuario"
            v-model="iptUsuario"
            outlined
            dense
          ></v-text-field>
          <v-text-field
            label="Senha"
            type="password"
            v-model="iptSenha"
            dense
            outlined
          ></v-text-field>
        </v-card-text>
        <v-card-actions>
          <v-btn color="primary" type="submit" block depressed :loading="loading">Entrar</v-btn>
        </v-card-actions>
      </v-form>
    </v-card>
  </v-container>
</template>

<script>
import AppWebClient from '@/http/AppWebClient';

export default {
  name: 'Login',
  data: () => ({
    loading: false,
    iptUsuario: '',
    iptSenha: '',
  }),
  methods: {
    async submitLoginForm() {
      if (!this.$refs['form-login'].validate()) return;
      this.loading = true;
      try {
        const appWebClient = new AppWebClient();
        const token = await appWebClient.login(this.iptUsuario, this.iptSenha);
        this.$store.commit('session/setToken', token);
        await this.$router.push('/');
      } finally {
        this.loading = false;
      }
    },
  },
}
</script>

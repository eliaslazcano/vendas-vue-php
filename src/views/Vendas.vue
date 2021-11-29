<template>
  <async-container :loading="loading"></async-container>
</template>

<script>
import AsyncContainer from '@/components/AsyncContainer';
import AppWebClient from '@/http/AppWebClient';
export default {
  name: 'Vendas',
  components: {AsyncContainer},
  data: () => ({
    loading: true,
    vendas: [],
  }),
  methods: {
    async loadData() {
      try {
        const appWebClient = new AppWebClient();
        this.vendas = appWebClient.vendas.listar();
      } finally {
        this.loading = false;
      }
    },
  },
  created() {
    this.loadData();
  }
}
</script>

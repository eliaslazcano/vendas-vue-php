<template>
  <v-snackbar
    :top="snackbar.top"
    :bottom="snackbar.bottom"
    :left="snackbar.left"
    :right="snackbar.right"
    :timeout="snackbar.timeout"
    :vertical="snackbar.vertical"
    :multi-line="snackbar.multiLine"
    :color="snackbar.color"
    :absolute="snackbar.absolute"
    :value="$store.state.snackbar.show"
    @input="$store.commit('snackbar/hide')"
    style="z-index: 204"
  >{{snackbar.text}}</v-snackbar>
</template>

<script>
import Snackbar from '@/models/Snackbar';
export default {
  name: 'GSnackbar',
  computed: {
    /**
     * Obtém a configuração do snackbar global
     * @returns {Snackbar}
     */
    snackbar() {
      return this.$store.state.snackbar.config ? this.$store.state.snackbar.config : new Snackbar();
    }
  }
}
</script>

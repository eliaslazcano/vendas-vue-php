<template>
  <v-overlay :value="$store.state.loader.loading" z-index="203">
    <v-progress-circular indeterminate size="48"></v-progress-circular>
  </v-overlay>
</template>

<script>
  export default {
    name: "GLoader"
  }
</script>

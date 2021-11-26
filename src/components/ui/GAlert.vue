<template>
  <div
    v-if="renderContainer"
    style="position: fixed; right: 0; top: 0; z-index: 204;"
    class="d-flex flex-column"
    :class="{'align-center': $vuetify.breakpoint.smAndDown, 'align-end': !$vuetify.breakpoint.smAndDown}"
  >
    <transition-group name="slide-x-reverse-transition" appear>
      <v-alert
        v-for="n in alerts"
        :key="n.id"
        :type="n.type"
        :border="n.border"
        :colored-border="n.coloredBorder"
        :dense="n.dense"
        :dismissible="n.dismissible"
        :class="{'mr-3': !$vuetify.breakpoint.smAndDown}"
        width="20rem"
        max-width="96%"
        elevation="2"
        class="mt-2 mb-0"
      >{{n.text}}</v-alert>
    </transition-group>
  </div>
</template>

<script>
export default {
  name: 'GAlert',
  data:() => ({
    renderContainer: false,
  }),
  computed: {
    /**
     * Alertas
     * @returns {array<Alert>}
     */
    alerts() {
      return this.$store.state.alert.alerts;
    },
  },
  watch: {
    alerts(v) {
      if (v.length > 0) this.renderContainer = true;
      else {
        setTimeout(() => this.renderContainer = false, 500);
      }
    },
  },
}
</script>

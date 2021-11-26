<template>
  <v-container class="d-flex flex-column" style="min-height: 100%" :fluid="fluid">
    <transition
      mode="out-in"
      :name="loading ? transition : spinnerTransition"
      :appear="appear"
      :leave-class="loading ? transition + '-leave' : spinnerTransition + '-leave'"
      :leave-to-class="loading ? transition + '-leave-to' : spinnerTransition + '-leave-to'"
      :leave-active-class="loading ? transition + '-leave-active' : spinnerTransition + '-leave-active'"
      :enter-class="loading ? spinnerTransition + '-enter' : transition + '-enter'"
      :enter-to-class="loading ? spinnerTransition + '-enter-to' : transition + '-enter-to'"
      :enter-active-class="loading ? spinnerTransition + '-enter-active' : transition + '-enter-active'"
    >
      <div v-if="loading" :key="0" class="d-flex justify-center align-center grow flex-column" style="height: 100%">
        <v-progress-circular
          indeterminate
          :color="color"
        ></v-progress-circular>
        <p v-if="typeof loading === 'string'" class="mb-0 mt-1">{{loading}}</p>
      </div>
      <div v-else :key="1">
        <slot/>
      </div>
    </transition>
  </v-container>
</template>

<script>
export default {
  name: 'AsyncContainer',
  props: {
    loading: {type: [Boolean, String], default: false},
    color: {type: String, default: 'blue'},
    transition: {type: String, default: 'scroll-x-transition'},
    spinnerTransition: {type: String, default: 'fade-transition'},
    fluid: {type: Boolean, default: false},
    appear: {type: Boolean, default: true},
  },
}
</script>

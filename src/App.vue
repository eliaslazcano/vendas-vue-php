<template>
  <v-app>
    <v-app-bar
      v-if="$store.state.session.token"
      class="d-print-none"
      :dense="$vuetify.breakpoint.smAndDown"
      :color="$store.state.dark ? 'secondary' : 'primary'"
      app
      dark
    >
      <v-app-bar-nav-icon @click.stop="showMenu = !showMenu" />
      <div class="d-flex align-center">
        <v-img
          alt="Vuetify Logo"
          class="shrink mr-2"
          contain
          src="https://cdn.vuetifyjs.com/images/logos/vuetify-logo-dark.png"
          transition="scale-transition"
          width="40"
        />

        <v-img
          alt="Vuetify Name"
          class="shrink mt-1 hidden-sm-and-down"
          contain
          min-width="100"
          src="https://cdn.vuetifyjs.com/images/logos/vuetify-name-dark.png"
          width="100"
        />
      </div>
      <v-spacer></v-spacer>
      <v-tooltip bottom v-if="$vuetify.breakpoint.smAndUp">
        <template v-slot:activator="{ on: tooltip }">
          <v-btn icon @click="$store.commit('setDark', !$store.state.dark)" v-on="tooltip">
            <v-icon v-if="$store.state.dark">mdi-white-balance-sunny</v-icon>
            <v-icon v-else>mdi-weather-night</v-icon>
          </v-btn>
        </template>
        <span>Modo escuro</span>
      </v-tooltip>
      <v-menu
        left
        bottom
        class="d-print-none"
        :dark="$store.state.dark"
      >
        <template v-slot:activator="{ on: menu, attrs }">
          <v-tooltip bottom>
            <template v-slot:activator="{ on: tooltip }">
              <v-btn icon v-on="{ ...tooltip, ...menu }" v-bind="attrs">
                <v-icon>mdi-dots-vertical</v-icon>
              </v-btn>
            </template>
            <span>Opções</span>
          </v-tooltip>
        </template>
        <v-list class="py-0" dense>
          <v-list-item @click="$store.commit('setDark', !$store.state.dark)" v-if="!$vuetify.breakpoint.smAndUp">
            <v-list-item-icon>
              <v-icon v-if="$store.state.dark">mdi-white-balance-sunny</v-icon>
              <v-icon v-else>mdi-weather-night</v-icon>
            </v-list-item-icon>
            <v-list-item-content>
              <v-list-item-title>Modo escuro</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-divider></v-divider>
          <v-list-item @click="$store.dispatch('session/signout')">
            <v-list-item-icon>
              <v-icon color="error">mdi-connection</v-icon>
            </v-list-item-icon>
            <v-list-item-content>
              <v-list-item-title>Desconectar</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </v-list>
      </v-menu>
    </v-app-bar>
    <v-navigation-drawer
      app
      v-if="$store.state.session.token"
      v-model="showMenu"
      :dark="$store.state.dark"
    >
      <template v-slot:prepend>
        <v-list class="py-0">
          <v-list-item to="/profile" class="pl-2" color="primary">
            <v-list-item-avatar>
              <v-avatar color="primary" class="font-weight-medium white--text elevation-2" style="border: 5px solid">
                <img src="assets/avatar.svg" alt="" style="object-fit: cover">
              </v-avatar>
            </v-list-item-avatar>
            <v-list-item-content>
              <v-list-item-title>{{$store.getters['session/payload'].usuario.nome}}</v-list-item-title>
              <v-list-item-subtitle>Funcionário</v-list-item-subtitle>
            </v-list-item-content>
          </v-list-item>
        </v-list>
      </template>
      <template v-slot:append>
        <div class="px-2 pb-1">
          <v-btn
            color="error"
            class="white--text"
            v-if="!$vuetify.breakpoint.lgAndUp"
            @click="$store.dispatch('session/signout')"
            small
            block
            outlined
          >
            <v-icon class="mr-1">mdi-connection</v-icon>Desconectar
          </v-btn>
        </div>
      </template>
    </v-navigation-drawer>
    <v-main :class="{'white--text': $store.state.dark, 'black': $store.state.dark, 'app-background': !$store.state.dark}">
      <router-view/>
    </v-main>
    <v-dialog width="500" max-width="96%" v-model="dialog" persistent>
      <v-card>
        <v-card-title
            class="red lighten-1 white--text py-2"
            style="background-color: #f5f5f5; border-bottom: 1px solid #dbdbdb"
        >Erro</v-card-title>
        <v-card-text class="mt-3">
          <div class="d-flex align-center">
            <v-icon size="48" class="mr-3" color="error">mdi-alert</v-icon>
            <p class="body-1 flex-grow-1 mb-0">{{$store.state.dialog.text}}</p>
          </div>
        </v-card-text>
        <v-card-actions
            class="justify-center"
            style="background-color: #f5f5f5; border-top: 1px solid #dbdbdb"
        >
          <v-btn
              small
              depressed
              color="secondary"
              @click="$store.commit('dialog/close')"
          >ENTENDI</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-app>
</template>

<script>

export default {
  name: 'App',
  data: () => ({
    showMenu: null,
  }),
  computed: {
    dialog() {
      return !!this.$store.state.dialog.text;
    },
  }
};
</script>

<style>
.app-background {
  background-color: #f4f4f4;
}
</style>

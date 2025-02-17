<template>
  <v-app>
    <loading-component v-if="$root.loading" />
    <div v-else>
      <nprogress-container></nprogress-container>
      <router-view></router-view>
    </div>
  </v-app>
</template>

<script>
import NprogressContainer from "vue-nprogress/src/NprogressContainer";
import LoadingComponent from "./components/LoadingComponent";

export default {
  name: "app",
  components: {
    NprogressContainer,
    LoadingComponent,
  },
};
</script>

<template>
  <LoadingScreen :ready="false">
    <div class="text-center"><ErrorBear /></div>
  </LoadingScreen>
</template>

<script>
import axios from "axios";
import { useUserStore } from "../../store/user";
import {router} from "../../helpers/router";
import ErrorBear from "../../components/app/ErrorBear.vue";

export default {
  name: "LoginLink",
  components: {ErrorBear},

  setup() {
    const userStore = useUserStore();

    return {
      markAsLoggedin: (payload) => userStore.markAsLoggedin(payload),
    };
  },

  data() {
    return {
      ready: false,
    }
  },

  mounted() {
    axios.get("/app/login_check" + window.location.search).then(response => {
      const user = response.data;
      this.markAsLoggedin({user});
      router.push('/site/home');
    }).catch(error => {
        this.ready = true;
    })
  }
}
</script>

<style scoped>

</style>

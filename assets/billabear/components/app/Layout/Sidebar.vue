<template>

</template>

<script>
import RoleOnlyView from "../RoleOnlyView.vue";
import {Select} from "flowbite-vue";
import { useUserStore } from "../../../store/user";
import { storeToRefs } from "pinia";

export default {
  name: "Sidebar",
  components: {Select, RoleOnlyView},

  setup() {
    const userStore = useUserStore();
    const { locale } = storeToRefs(userStore);

    return {
      locale,
      updateLocale: (payload) => userStore.updateLocale(payload),
    };
  },

  data() {
    return {
      origin: '',
    }
  },

  methods: {
    changeLocale: function(value) {
      this.updateLocale({locale: value.target.value})
      this.$i18n.locale = this.locale;
    },
    updateValue(event) {
      this.localSelectedValue = event.target.value;
    },
  },
  mounted() {
    const sidebar = document.getElementById('sidebar');

    this.origin = window.location.hostname;

  }
}
</script>

<style scoped>

</style>

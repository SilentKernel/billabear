<template>
  <LoadingScreen :ready="ready" v-if="!error">

    <div class="h-screen w-screen md:flex">
      <div class="hidden md:block sticky w-72 bg-teal-500 text-white overflow-auto">
        <div class="py-12 px-5 text-white">
          <img src="/images/app-logo.png" alt="BillaBear" class="h-12">
        </div>
        <MenuDesktop />
      </div>
      <div class="md:hidden bg-teal-500 text-white">
        <MobileDesktop />
      </div>
      <div class="md:flex-1 bg-gray-100 p-5 divide-y divide-gray-200 overflow-auto h-screen">
        <div class="text-end text-gray-500 font-bold pb-2">
          <TopMenu />
        </div>
        <div class="pt-2 ">
          <div class="rounded-xl p-3 bg-red-500 text-white font-bold" v-if="!has_stripe_key">
            {{ $t('app.onboarding.main.bar.message') }}
          </div>
          <router-view></router-view>
        </div>
      </div>

    </div>

  </LoadingScreen>
  <div v-else class="h-screen flex w-screen items-center justify-center">
    {{ $t('app.onboarding.main.error') }}
  </div>
</template>

<script>
import axios from "axios";
import { useOnboardingStore } from "../../store/onboarding";
import { useUserStore } from "../../store/user";
import { storeToRefs } from "pinia";
import MenuDesktop from "../../components/app/Layout/MenuDesktop.vue";
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
import TopMenu from "../../components/app/Layout/TopMenu.vue";
import MobileDesktop from "../../components/app/Layout/MobileMenu.vue";

export default {
  name: "InternalApp",
  components: {
    MobileDesktop,
    TopMenu,
    MenuDesktop,
    Menu,
    MenuButton,
    MenuItems,
    MenuItem
  },

  setup() {
    const onboardingStore = useOnboardingStore();
    const userStore = useUserStore();

    const {
      has_stripe_key,
      has_stripe_imports,
      has_subscription_plan,
      has_customer,
      has_subscription,
      has_product,
      show_onboarding,
      ready,
      error
    } = storeToRefs(onboardingStore);

    const { locale } = storeToRefs(userStore);

    return {
      has_stripe_key,
      has_stripe_imports,
      has_subscription_plan,
      has_customer,
      has_subscription,
      has_product,
      show_onboarding,
      ready,
      error,
      locale,
      setStripeImport: (payload) => onboardingStore.setStripeImport(payload),
      stripeImport: () => onboardingStore.stripeImport(),
      fetchData: () => onboardingStore.fetchData(),
    };
  },

  data() {
    return {
      is_update_available: false,
      has_default_tax: false,
      origin: '',
      has_api_key: true,
    }
  },

  methods: {
    dimissStripeImport: function() {
      axios.post('/app/settings/stripe-import/dismiss').then(response => {
        this.stripeImport();
      })
    },
    dimissUpdateNotification: function() {
      axios.post('/app/settings/update/dismiss').then(response => {
        this.is_update_available = false;
      })
    },
  },

  mounted() {
    this.origin = window.location.hostname;
    this.fetchData().then(response => {
      this.$i18n.locale = this.locale;
    });
  }
}
</script>

<style scoped>

</style>

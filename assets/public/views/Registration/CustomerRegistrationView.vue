<template>
  <div class="h-screen flex flex-col lg:flex-row" v-if="ready">
    <!-- Left Section -->
    <div class="bg-primary-500 w-full lg:w-1/2 h-full desktop-only text-center text-white pt-12">
      <img src="/images/app-logo.png" alt="Ludovic Frank" class=" mx-auto" />

      <h1 class="my-3 text-4xl underline">{{ $t('portal.registration.title') }}</h1>

      <div class="mt-5 mx-auto max-w-2xl text-left bg-white text-black p-3 rounded-lg">
        <h2 class="text-3xl">{{ registration.name }}</h2>
        <p class="text-lg mt-3">{{ $t('portal.registration.description') }}</p>
      </div>
    </div>
    <div class="p-12 w-full lg:w-1/2">
      <div v-if="stage === 'customer'">
        <h2 class="text-2xl font-bold mb-5">{{ $t('portal.registration.customer.title') }}</h2>
        <div class="form-field-ctn">
          <label class="form-field-lbl" for="email">
            {{ $t('portal.registration.customer.fields.email') }}
          </label>
          <p class="form-field-error" v-if="errors.email != undefined">{{ errors.email }}</p>
          <input type="email" class="rounded-lg p-2 border-gray-300 text-gray-900 shadow w-full" id="email" v-model="customer.email" />
        </div>

        <div class="form-field-ctn">
          <label class="form-field-lbl" for="name">
            {{ $t('portal.registration.customer.fields.name') }}
          </label>
          <p class="form-field-error" v-if="errors.name != undefined">{{ errors.name }}</p>
          <input type="text" class="rounded-lg p-2 border-gray-300 text-gray-900 shadow w-full" id="name" v-model="customer.name" />
        </div>

        <div class="form-field-ctn">
          <label class="form-field-lbl" for="country">
            {{ $t('portal.registration.customer.fields.country') }}
          </label>
          <p class="form-field-error" v-if="errors['address.country'] != undefined">{{ errors['address.country'] }}</p>
          <CountrySelect class="rounded-lg p-2 border-gray-300 text-gray-900 shadow-lg w-full"  v-model="customer.address.country" />
        </div>

        <div class="form-field-ctn mt-2">
          <SubmitButton :in-progress="sending" class="w-full shadow-lg btn--main" @click="createCustomer">{{ $t('portal.registration.customer.submit') }}</SubmitButton>
        </div>
      </div>
      <div v-else-if="stage == 'payment'">
        <h2 class="text-xl mb-5">{{ $t('portal.registration.payment.title') }}</h2>
        <div id="cardInput" class="my-5"></div>
        <div id="cardError"></div>
        <SubmitButton :in-progress="sending" class="w-full btn--main" @click="addPaymentMethod">{{ $t('portal.registration.payment.submit') }}</SubmitButton>
      </div>
      <div v-else>
        <h2 class="text-xl mb-5">{{ $t('portal.registration.success.title') }}</h2>
        <p>{{ $t('portal.registration.success.message') }}</p>
      </div>
    </div>
  </div>
  <div class="flex justify-center items-center h-screen" v-else>
    <img src="/images/public-logo.png" class="w-80 animate-fade-in-out" alt="Ludovic Frank" />
  </div>
</template>

<script>
import axios from "axios";
import {stripeservice} from "../../../billabear/services/stripeservice";
import CountrySelect from "../../../billabear/components/app/Forms/CountrySelect.vue";

export default {
  name: "CustomerRegistrationView",
  components: {CountrySelect},
  data() {
    return {
      error_page: false,
      stage: 'customer',
      registration: {},
      customer: {address: {}},
      ready: false,
      errors: {},
      sending: false,
      stripe: null,
      stripeConfig: {},
      customerId: null,
      card: {}
    }
  },
  mounted() {
    const slug = this.$route.params.slug;
    axios.get("/public/register/"+slug+"/view").then(response => {
      this.ready = true;
      this.registration = response.data;
    }).catch(error => {
      this.ready = true;
      if (error.response !== undefined && error.response.status === 404) {
        this.error_page = true;
        return;
      }
      this.error_page = true;
    })
  },
  methods: {
    createCustomer: function () {
      const slug = this.$route.params.slug;

      this.sending = true;
      const imported = document.createElement('script');
      imported.src = 'https://js.stripe.com/v3/';
      document.head.appendChild(imported);

      axios.post(`/public/register/${slug}/customer`, this.customer).then(response => {
        this.stage = 'payment';
        this.stripeConfig = response.data.stripe;
        this.customerId = response.data.customer_id;
        this.ready = true;
        this.stripe = Stripe(this.stripeConfig.key);
        this.sending = false;
        const that = this;
        setTimeout(()=> {
          that.card = stripeservice.getCardToken(that.stripe, that.stripeConfig.token);
        }, 500)
      }).catch(error => {
        this.sending = false;
        if (error.response && error.response.data && error.response.data.errors) {
          this.errors = error.response.data.errors;
        }
      })
    },
    addPaymentMethod: function () {
      const slug = this.$route.params.slug;
      this.sending = true;
      const that = this;

      stripeservice.sendCard(this.stripe, this.card).then(
          response => {
            const token = response.token.id;
            axios.post(`/public/register/${slug}/payment-method`, {
              token: token,
              customer_id: that.customerId
            }).then(response => {
              if (response.data.success) {
                that.stage = 'success';
              } else {
                this.general_error = true;
              }
              that.sending = false;
            }).catch(error => {
              that.sending = false;
              alert('Failed to add payment method. Please try again.');
            })
          }
      ).catch(error => {
        that.sending = false;
        alert('Failed to process card. Please try again.');
      })
    }
  }
}
</script>

<style scoped>
</style>

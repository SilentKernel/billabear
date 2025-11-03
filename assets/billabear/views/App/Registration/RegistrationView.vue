<template>
  <div>
    <h1 class="mt-5 ml-5 page-title">{{ $t('app.registration.view.title') }}</h1>

    <LoadingScreen :ready="ready">
      <div class="card-body">
        <h2 class="section-header">{{ $t('app.registration.view.details.title') }}</h2>
        <div class="section-body">
          <dl class="detail-list">
            <div>
              <dt>{{ $t('app.registration.view.details.name') }}</dt>
              <dd>{{ registration.name }}</dd>
            </div>
            <div>
              <dt>{{ $t('app.registration.view.details.status') }}</dt>
              <dd>
                <span v-if="registration.valid" class="text-green-600">{{ $t('app.registration.view.details.active') }}</span>
                <span v-else class="text-red-600">{{ $t('app.registration.view.details.inactive') }}</span>
              </dd>
            </div>
            <div>
              <dt>{{ $t('app.registration.view.details.permanent') }}</dt>
              <dd>
                <span v-if="registration.permanent">{{ $t('app.registration.view.details.yes') }}</span>
                <span v-else>{{ $t('app.registration.view.details.no') }}</span>
              </dd>
            </div>
            <div>
              <dt>{{ $t('app.registration.view.details.created_at') }}</dt>
              <dd>{{ $filters.moment(registration.created_at, 'lll') }}</dd>
            </div>
          </dl>
        </div>
      </div>

      <div class="card-body mt-4">
        <h2 class="section-header">{{ $t('app.registration.view.link.title') }}</h2>
        <div class="section-body">
          <p class="mb-3">{{ $t('app.registration.view.link.description') }}</p>

          <div class="form-field-ctn">
            <label class="form-field-lbl">
              {{ $t('app.registration.view.link.url') }}
            </label>
            <div class="flex gap-2">
              <input type="text" class="form-field flex-1" readonly :value="fullUrl" ref="urlInput" />
              <button @click="copyToClipboard" class="btn--main">
                <i class="fa-solid fa-copy"></i> {{ $t('app.registration.view.link.copy') }}
              </button>
            </div>
            <p v-if="copied" class="text-green-600 mt-2">{{ $t('app.registration.view.link.copied') }}</p>
          </div>

        </div>
      </div>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "RegistrationView",
  data() {
    return {
      ready: false,
      registration: {},
      copied: false
    }
  },
  computed: {
    fullUrl: function() {
      return window.location.origin + this.registration.registration_url;
    }
  },
  mounted() {
    this.loadRegistration();
  },
  methods: {
    loadRegistration: function () {
      const id = this.$route.params.id;
      axios.get(`/app/registration/${id}`).then(response => {
        this.registration = response.data;
        this.ready = true;
      }).catch(error => {
        this.$router.push({name: 'app.registration.list'});
      });
    },
    copyToClipboard: function () {
      this.$refs.urlInput.select();
      document.execCommand('copy');
      this.copied = true;
      setTimeout(() => {
        this.copied = false;
      }, 3000);
    }
  }
}
</script>

<style scoped>
</style>

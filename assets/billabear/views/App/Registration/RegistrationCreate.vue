<template>
  <div>
    <h1 class="ml-5 mt-5 page-title">{{ $t('app.registration.create.title') }}</h1>

    <LoadingScreen :ready="ready">
      <div class="card-body">
        <div class="form-field-ctn">
          <label class="form-field-lbl" for="name">
            {{ $t('app.registration.create.fields.name') }}
          </label>
          <p class="form-field-error" v-if="errors.name != undefined">{{ errors.name }}</p>
          <input type="text" class="form-field" id="name" v-model="registration.name" />
          <p class="form-field-help">{{ $t('app.registration.create.help_info.name') }}</p>
        </div>

        <div class="form-field-ctn">
          <label class="form-field-lbl" for="permanent">
            <input type="checkbox" id="permanent" v-model="registration.permanent" />
            {{ $t('app.registration.create.fields.permanent') }}
          </label>
          <p class="form-field-help">{{ $t('app.registration.create.help_info.permanent') }}</p>
        </div>

        <SubmitButton :in-progress="sending" @click="send">
          {{ $t('app.registration.create.submit_btn') }}
        </SubmitButton>
      </div>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "RegistrationCreate",
  data() {
    return {
      ready: true,
      sending: false,
      registration: {
        name: '',
        permanent: true
      },
      errors: {}
    }
  },
  methods: {
    send: function () {
      this.sending = true;
      this.errors = {};

      axios.post('/app/registration', this.registration).then(response => {
        this.$router.push({name: 'app.registration.view', params: {id: response.data.id}})
      }).catch(error => {
        this.sending = false;
        if (error.response && error.response.data && error.response.data.errors) {
          this.errors = error.response.data.errors;
        }
      })
    }
  }
}
</script>

<style scoped>
</style>

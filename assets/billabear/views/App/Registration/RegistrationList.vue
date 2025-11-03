<template>
  <div v-if="!has_error">
    <div class="grid grid-cols-2">
      <h1 class="ml-5 mt-5 page-title">{{ $t('app.registration.list.title') }}</h1>
      <div class="mt-5 top-button-container text-end">
        <RoleOnlyView role="ROLE_ACCOUNT_MANAGER">
          <router-link :to="{name: 'app.registration.create'}" class="btn--main ml-4">
            <i class="fa-solid fa-plus"></i> {{ $t('app.registration.list.create_new') }}
          </router-link>
        </RoleOnlyView>
      </div>
    </div>
    <LoadingScreen :ready="ready">
      <div class="pl-5 flex-1">
        <div class="rounded-lg bg-white shadow p-3">
          <table class="w-full">
            <thead>
            <tr class="border-b border-black">
              <th class="text-left pb-2">{{ $t('app.registration.list.list.name') }}</th>
              <th class="text-left pb-2">{{ $t('app.registration.list.list.created_at') }}</th>
              <th class="text-left pb-2">{{ $t('app.registration.list.list.status') }}</th>
              <th></th>
            </tr>
            </thead>
            <tbody v-if="loaded">
            <tr v-for="registration in registrations" class="mt-5">
              <td class="py-3">{{ registration.name }}</td>
              <td class="py-3">{{ $filters.moment(registration.created_at, 'lll') }}</td>
              <td class="py-3">
                <span v-if="registration.valid" class="text-green-600">{{ $t('app.registration.list.list.active') }}</span>
                <span v-else class="text-red-600">{{ $t('app.registration.list.list.inactive') }}</span>
              </td>
              <td class="py-3">
                <router-link :to="{name: 'app.registration.view', params: {id: registration.id}}" class="list-btn">
                  {{ $t('app.registration.list.list.view') }}
                </router-link>
              </td>
            </tr>
            <tr v-if="registrations.length === 0">
              <td colspan="4" class="py-3 text-center">{{ $t('app.registration.list.no_registrations') }}</td>
            </tr>
            </tbody>
            <tbody v-else>
            <tr>
              <td colspan="4" class="py-3 text-center">
                <LoadingMessage>{{ $t('app.registration.list.loading') }}</LoadingMessage>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
        <div class="sm:grid sm:grid-cols-2 mt-4">
          <div>
            <button @click="prevPage" v-if="show_back" class="btn--main mr-3">
              {{ $t('app.registration.list.prev') }}
            </button>
            <button @click="nextPage" v-if="has_more" class="btn--main">
              {{ $t('app.registration.list.next') }}
            </button>
          </div>
          <div class="text-end">
            <select class="rounded-lg border border-gray-300" @change="changePerPage" v-model="per_page">
              <option value="10">10</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>
          </div>
        </div>
      </div>
    </LoadingScreen>
  </div>
  <div v-else class="error-page">
    {{ $t('app.registration.list.error_message') }}
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "RegistrationList",
  data() {
    return {
      ready: false,
      registrations: [],
      has_more: false,
      has_error: false,
      loaded: false,
      last_key: null,
      first_key: null,
      show_back: false,
      per_page: "10",
    }
  },
  mounted() {
    this.loadRegistrations();
  },
  watch: {
    '$route.query': function () {
      this.loadRegistrations()
    }
  },
  methods: {
    nextPage: function () {
      const queryVals = {last_key: this.last_key};
      if (this.$route.query.per_page) {
        queryVals.per_page = this.$route.query.per_page;
      }
      this.$router.push({query: queryVals})
    },
    prevPage: function () {
      const queryVals = {first_key: this.first_key};
      if (this.$route.query.per_page) {
        queryVals.per_page = this.$route.query.per_page;
      }
      this.$router.push({query: queryVals})
    },
    changePerPage: function ($event) {
      const queryVals = {per_page: $event.target.value};
      this.$router.push({query: queryVals});
    },
    loadRegistrations: function () {
      this.loaded = false;
      let urlString = '/app/registration?';

      if (this.$route.query.last_key !== undefined) {
        urlString += '&last_key=' + encodeURIComponent(this.$route.query.last_key);
        this.show_back = true;
      } else if (this.$route.query.first_key !== undefined) {
        urlString += '&first_key=' + encodeURIComponent(this.$route.query.first_key);
        this.has_more = true;
      }

      if (this.$route.query.per_page !== undefined) {
        urlString += '&per_page=' + this.$route.query.per_page;
        this.per_page = this.$route.query.per_page;
      }

      axios.get(urlString).then(response => {
        this.registrations = response.data.data;
        if (response.data.has_more !== undefined) {
          this.has_more = response.data.has_more;
        }
        if (response.data.last_key !== undefined) {
          this.last_key = response.data.last_key;
        }
        if (response.data.first_key !== undefined) {
          this.first_key = response.data.first_key;
        }
        this.loaded = true;
        this.ready = true;
      }).catch(error => {
        this.has_error = true;
      })
    }
  }
}
</script>

<style scoped>
</style>

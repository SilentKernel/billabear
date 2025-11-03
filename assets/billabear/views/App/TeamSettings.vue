<template>
  <div>
    <h1 class="page-title">{{ $t('app.team.main.title') }}</h1>

    <div class="top-button-container">
      <button class="btn--main" :class="{'btn--main--disabled': show_invite_form}"  @click="showInviteForm" :disabled="show_invite_form"><i class="fa-solid fa-user-plus"></i> {{ $t('app.team.main.add_team_member') }}</button>
    </div>

    <TeamInvite v-if="show_invite_form" />

    <TeamMembers />

    <TeamPendingInvites />
  </div>
</template>

<script>
import { useTeamStore } from "../../store/team";
import { storeToRefs } from "pinia";
import TeamInvite from "../../components/app/Team/TeamInvite.vue";
import TeamPendingInvites from "../../components/app/Team/TeamPendingInvites.vue";
import TeamMembers from "../../components/app/Team/TeamMembers.vue";

export default {
  name: "TeamSettings",
  components: {TeamMembers, TeamPendingInvites, TeamInvite},

  setup() {
    const teamStore = useTeamStore();
    const { show_invite_form, sent_invites, members } = storeToRefs(teamStore);

    return {
      show_invite_form,
      sent_invites,
      members,
      showInviteForm: () => teamStore.showInviteForm(),
      loadTeamInfo: () => teamStore.loadTeamInfo(),
    };
  },

  mounted() {
    this.loadTeamInfo()
  },

  methods: {
    displayInviteForm: function () {
      this.showInviteForm()
    }
  }
}
</script>

<style scoped>

</style>

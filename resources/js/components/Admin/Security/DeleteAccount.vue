<template>
  <div>
    <div class="setting-info">
      <h2>Delete account</h2>
      <p>
        Once your account is deleted, all of its resources and data will be
        permanently deleted. Before deleting your account, please download any
        data or information that you wish to retain.
      </p>
    </div>
    <UserDeleteDialog />
  </div>
</template>

<script>
import UserDeleteDialog from "../../../components/UserDeleteDialog.vue";

export default {
  name: "DeleteAccount",
  components: { UserDeleteDialog },
};
</script>

<style scoped>
.setting-info {
  width: 70%;
  font-family: Montserrat, sans-serif !important;
  line-height: 1.4 !important;
  letter-spacing: -0.1px !important;
}

.setting-info h2 {
  font: 500 20px/32px Montserrat, sans-serif;
  letter-spacing: normal;
  margin: 0 0 16px;
  color: rgba(0, 0, 0, 0.87);
}

.setting-info p {
  font-size: 16px;
  color: rgba(28, 37, 44, 0.85);
}
</style>

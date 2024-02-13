<script setup></script>

<template>
  <main>
    <h2>Login</h2>
    <div class="content">
      <p v-if="message" v-html="message"></p>
      <UniversalForm
        :data="form"
        :settings="settings"
        @submit-form="onSubmit"
      ></UniversalForm>
    </div>
  </main>
</template>

<script>
import { ref, defineComponent } from "vue";
import { useUserStore } from "@/stores/user";
import UniversalForm from "@/components/tools/UniversalForm.vue";

export default defineComponent({
  components: { UniversalForm },
  name: "Login",
  setup: () => {
    /**
     * EXPORTIETE VARIABLEN
     */
    const labels = ref({
      missing_fields:
        "In folgenden Feldern müssen gültige Werte eingetragen werden:",
      password: "Passwort",
      username: "Nutzername",
    });
    const message = ref(null);
    const password = ref("");
    const username = ref("");
    const form = ref({
      username: "",
      password: "",
    });

    const settings = {
      attributes: {},
      input_formatters: {},
      labels: {
        username: labels.value.username,
        password: labels.value.password,
      },
      labelSubmitBtn: "einloggen",
      output_formatters: {},
      writable: {
        username: "text",
        password: "password",
      },
    };

    /**
     * PRIVATE VARIABLEN
     */
    const required = {
      password: labels.value.password,
      username: labels.value.username,
    };

    /**
     * onSubmit()
     *
     * Action-Handler vom Login-Formular
     */
    async function onSubmit() {
      const userStore = useUserStore();
      let errors = [];

      // Überprüfen, ob alle benötigten Felder belegt sind
      for (let i in required) {
        if (!("" < form.value[i])) {
          errors.push(required[i]);
        }
      }

      if (!errors.length) {
        return userStore
          .login(form.value.username, form.value.password)
          .catch((error) => {
            message.value = "apiError: " + error;
          });
      } else {
        message.value =
          labels.value.missing_fields +
          "<ul><li>" +
          errors.join("</li><li>") +
          "</li><ul>";
      }
    }

    return { form, labels, message, onSubmit, password, settings, username };
  },
});
</script>
<style scoped>
form{  border: 1px solid #999;
  padding: 10px;
}
</style>

<script setup>
</script>

<template>
  <main>
    <h2>Login</h2>
    <form @submit.prevent="onSubmit">
      <p v-if="message" v-html="message"></p>
      <div class="form-group">
        <label for="username">{{ labels.username }}</label>
        <input name="username" id="username" type="text" v-model="form.username" />
      </div>
      <div class="form-group">
        <label for="password">{{ labels.password }}</label>
        <input name="password" id="password" type="password" v-model="form.password" />
      </div>
      <div class="form-group">
        <input type="submit">
      </div>
    </form>
  </main>
</template>

<script>
import { ref, defineComponent } from "vue"
import { useUserStore } from '@/stores/user';

export default defineComponent({
  name: "Login",
  setup: () => {
    /**
     * EXPORTIETE VARIABLEN
     */
    const labels = ref({
      missing_fields: "In folgenden Feldern müssen gültige Werte eingetragen werden:",
      password: "Passwort",
      username: "Nutzername"
    });
    const message = ref(null);
    const password = ref("9^*F2vv%gfuftB6VkR");
    const username = ref("kai");
    const form = ref({
      password: "9^*F2vv%gfuftB6VkR",
      username: "ka",
    })


    /**
     * PRIVATE VARIABLEN
     */
    const required = {
      password: labels.value.password,
      username: labels.value.username
    }

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
        return userStore.login(form.value.username, form.value.password)
          .catch(error => { message.value = "apiError: " + error });
      }
      else {
        message.value = labels.value.missing_fields + "<ul><li>" + errors.join("</li><li>") + "</li><ul>"
      }
    }

    return { form, labels, message, onSubmit, password, username }
  },
});
</script>
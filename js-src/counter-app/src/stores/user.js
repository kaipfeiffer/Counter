import { ref, computed } from "vue";
import { defineStore } from "pinia";
import router from "@/router";
import { api } from "@/modules/auth-api";
const baseUrl = `${import.meta.env.VITE_API_URL}`;

export const useUserStore = defineStore("user", () => {
  /**
   * exportierte State variablen
   */
  const user = ref(JSON.parse(localStorage.getItem("user")));
  const targetUrl = ref(null);

  // console.log("BaseUrl "+baseUrl);

  /**
   * login
   *
   * lmeldet den Nutzer mit den übermittelten Credentials an
   *
   * @param string  username
   * @param string  password
   */
  async function login(login_name, password) {
    const api_user = await api.post(`${baseUrl}login`, {
      login_name: login_name,
      password: password,
    });

    console.log(api_user);

    if (api_user.jwt) {
      // update pinia state
      user.value = {
        token: api_user.jwt,
        ctag: api_user.ctag,
        firstname: api_user.firstname,
        lastname: api_user.lastname,
      };
    }
    console.log("Save User");
    // store user details and jwt in local storage to keep user logged in between page refreshes
    localStorage.setItem("user", JSON.stringify(user.value));

    // redirect to previous url or default to home page
    router.push(targetUrl.value || "/");
  }

  /**
   * logout
   *
   * loggt den Nutzer wieder aus
   */
  function logout() {
    user.value = null;
    localStorage.removeItem("user");
    router.push("/login");
  }

  /**
   * logout
   *
   * loggt den Nutzer wieder aus
   */
  function setCtag(ctag) {
    console.log("ctag: " + ctag);
    user.value.ctag = ctag;
    localStorage.setItem("user", JSON.stringify(user.value));
  }

  return { login, logout, setCtag, targetUrl, user };
});

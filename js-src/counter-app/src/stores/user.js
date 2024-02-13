import { ref, computed } from "vue";
import { defineStore } from "pinia";
import router from "@/router";
import { api } from "@/modules/auth-api";

const server =
  0 <= window.location.href.indexOf("http://localhost")
    ? "http://localhost:8180"
    : "";
const baseUrl = server + `${import.meta.env.VITE_API_URL}`;

export const useUserStore = defineStore("user", () => {
  /**
   * exportierte State variablen
   */
  const user = ref(JSON.parse(localStorage.getItem("user")));
  let previousUser = JSON.parse(localStorage.getItem("previousUser")) ?? {};
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

    console.log("Save User");

    if (api_user.jwt) {
      let previousUser  = JSON.parse(localStorage.getItem("previousUser"));
      console.log(previousUser);
      // update pinia state
      if (user.value) {
        if (
          user.value?.firstname === api_user.firstname &&
          user.value?.lastname === api_user.lastname
        ) {
          // remove old token
          user.value.token  = api_user.jwt;
          user.value.ctag   = previousUser?.ctag ?? 0;
        }
      } else {
        user.value = {
          token: api_user.jwt,
          ctag: 0,
          loginname: api_user.loginname,
          firstname: api_user.firstname,
          lastname: api_user.lastname,
        };
        let loginname =   previousUser?.loginname
        if (loginname !== api_user.loginname) {
          localStorage.removeItem("counters");
          localStorage.removeItem("readings");
        }
      }
    }

    if (user.value) {
      // store user details and jwt in local storage to keep user logged in between page refreshes
      localStorage.setItem("user", JSON.stringify(user.value));
      let previousUser = {
        loginname: api_user.loginname,
        ctag: user.value.ctag,
      };
      localStorage.setItem("previousUser", JSON.stringify(previousUser));
    }
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
    // console.log("ctag: " + ctag);
    user.value.ctag   = ctag;
    previousUser.ctag = ctag;
    localStorage.setItem("user", JSON.stringify(user.value));
    localStorage.setItem("previousUser", JSON.stringify(previousUser));
  }

  return { login, logout, setCtag, targetUrl, user };
});

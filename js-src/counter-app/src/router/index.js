import { createRouter, createWebHistory } from "vue-router";
import HomeView from "../views/HomeView.vue";
import { useUserStore } from "@/stores/user";

// console.log(window.location.href.match(/https{0,1}\:\/\/[\w\.]+:{0,1}\d*(\/[^\/]+)/)[1]);
const appBaseDir =
  window.location.href.match(
    /https{0,1}\:\/\/[\w\.]+:{0,1}\d*((\/[^\/]+){0,2})\/.*$/
  )?.[1] ?? "";
// console.log(
//   appBaseDir,
//   window.location.href.match(
//     /https{0,1}\:\/\/[\w\.]+:{0,1}\d*((\/[^\/]+\/){0,2}).*$/
//   )
// );
const router = createRouter({
  base: "/kpm",
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: appBaseDir + "/",
      // alias: "/kpm-counter",
      name: "home",
      component: HomeView,
    },
    {
      path: appBaseDir + "/counter/:id?/:action?/:type?",
      // alias: "/kpm-counter/counter/:id",
      name: "counter",
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import("../views/CounterView.vue"),
    },
    {
      path: appBaseDir + "/counterform/:id",
      // alias: "/kpm-counter/counter/:id",
      name: "counterform",
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import("../views/CounterFormView.vue"),
    },
    {
      path: appBaseDir + "/counterinfo/:id?/:type?/:year?",
      name: "counterinfo",
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import("../views/CounterInfoView.vue"),
    },
    {
      path: appBaseDir + "/reading/:id?/:action?/:counter_id?",
      // alias: "/kpm-counter/reading/:id",
      name: "reading",
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import("../views/ReadingView.vue"),
    },
    {
      path: appBaseDir + "/login",
      // alias: "/kpm-counter/login",
      name: "login",
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import("../views/LoginView.vue"),
    },
  ],
});

router.beforeEach(async (to) => {
  // redirect to login page if not logged in and trying to access a restricted page
  const publicPages = [appBaseDir + "/login"];
  const authRequired = !publicPages.includes(to.path);
  // const auth = [];
  const auth = useUserStore();

  if (authRequired && !auth.user) {
    auth.targetUrl = to.fullPath;
    return appBaseDir + "/login";
    // return "/login";
  }
});

export default router;

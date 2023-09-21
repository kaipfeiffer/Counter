import { createRouter, createWebHistory } from "vue-router"
import HomeView from "../views/AboutView.vue"
import { useUserStore } from '@/stores/user';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/",
      // alias: "/kpm-counter",
      name: "home",
      component: HomeView
    },
    {
      path: "/counter/:id",
      // alias: "/kpm-counter/counter/:id",
      name: "counter",
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import("../views/CounterView.vue")
    },
    {
      path: "/reading/:id",
      // alias: "/kpm-counter/reading/:id",
      name: "reading",
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import("../views/ReadingView.vue")
    },
    {
      path: "/login",
      // alias: "/kpm-counter/login",
      name: "login",
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import("../views/LoginView.vue")
    }
  ]
})


router.beforeEach(async (to) => {
  // redirect to login page if not logged in and trying to access a restricted page
  const publicPages = ["/login","/kpm-counter/login"];
  const authRequired = !publicPages.includes(to.path);
  // const auth = [];
  const auth = useUserStore();

  if (authRequired && !auth.user) {
      auth.targetUrl = to.fullPath;
      // return "/kpm-counter/login";
      return "/login";
  }
});

export default router

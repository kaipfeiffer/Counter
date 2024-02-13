import { useUserStore } from "@/stores/user";

export const api = {
  delete: request("DELETE"),
  get: request("GET"),
  patch: request("PATCH"),
  post: request("POST"),
  put: request("PUT"),
};

function request(method) {
  return (url, body) => {
    const options = {
      method,
      headers: authHeader(url),
    };

    // console.log("request", method, url, body);
    if (body) {
      switch (method) {
        case "GET": {
          let params = [];
          for (let i in body) {
            params.push(i + "=" + body[i]);
          }
          url += "/?" + params.join("&");
          break;
        }
        default: {
          options.headers["Content-Type"] = "application/json";
          options.body = JSON.stringify(body);
        }
      }
    }
    // console.log("URL", url);
    // console.log(options);
    return fetch(url, options).then(handleResponse);
  };
}

// helper functions

function authHeader(url) {
  // return auth header with jwt if user is logged in and request is to the api url
  const { user } = useUserStore();
  const token = user?.token;
  // Server bei "localhost" anpassen, ansonsten nur die relative Adresse aus der ".env"-Datei nutzen
  const server =
    0 <= window.location.href.indexOf("http://localhost")
      ? "http://localhost:8180"
      : "";
  const isApiUrl = url.startsWith(server + import.meta.env.VITE_API_URL);

  // console.log(
  //   window.location.href.indexOf("http://localhost"),
  //   server + import.meta.env.VITE_API_URL
  // );
  //   console.log(user);
  if (token && isApiUrl) {
    return { Authorization: `Bearer ${token}` };
  } else {
    return {};
  }
}

function handleResponse(response) {
  return response.text().then((text) => {
    const data = text ? JSON.parse(text) : [];
    // console.log(text, data);
    if (!response.ok) {
      const { user, logout } = useUserStore();
      console.log("response", user, response);
      if ([401, 403].includes(response.status) && user) {
        // auto logout if 401 Unauthorized or 403 Forbidden response returned from api
        logout();
      }

      const error = (data && data.message) || response.statusText;
      return Promise.reject(error);
    }

    // console.log(data);
    return data;
  });
}

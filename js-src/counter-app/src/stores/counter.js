import { ref, computed } from "vue";
import { defineStore } from "pinia";
import { api } from "@/modules/auth-api";
import { useUserStore } from "@/stores/user";
import { setDecimalPlaces } from "@/modules/tools";

const server =
  0 <= window.location.href.indexOf("http://localhost")
    ? "http://localhost:8180"
    : "";
const baseUrl = server + `${import.meta.env.VITE_API_URL}`;

export const useCounterStore = defineStore("counter_store", () => {
  const usersStore = useUserStore();
  /**
   * exportierte State variablen
   */
  const readings = ref({});
  const counters = ref({});
  const measures = ref({
    1: "kw/h",
    2: "m³",
    3: "l",
    4: "kg",
    5: "t",
    6: "fm",
    7: "rm",
    8: "srm",
  });
  const type = ref(1);
  const current = ref({});
  const getForm = computed(() => {
    let dataRow = {};

    // console.log(loginname, usersStore.user.loginname);
    if (loginname !== usersStore.user.loginname) {
      counters.value = {};
      readings.value = {};
      readcounters(usersStore.user.ctag);
      readreadings(usersStore.user.ctag);
      usersStore.setCtag(storeCtag);
      loginname = usersStore.user.loginname;
    }

    for (let i in counters.value) {
      let counter = counters.value[i];
      current.value = {};
      // console.log(
      //   counter,
      //   type.value,
      //   parseInt(counter.counter_type),
      //   parseInt(type.value)
      // );
      if (
        null === type.value ||
        parseInt(counter.counter_type) === parseInt(type.value)
      ) {
        // console.log("inner");
        dataRow[i] = {
          name: counter.counter_name,
          reading: setDecimalPlaces(getLastReading(i), 1, "always"),
          consumption: setDecimalPlaces(getLastReading(i), 1, "always"),
          measure: counter["measure"] ? measures.value[counter.measure] : "",
          id: counter.id,
        };
        current.value[counter["id"]] = null;
      }
    }
    // console.log(dataRow);
    return dataRow;
  });
  const loading = ref(true);
  const page = ref(0);
  const page_size = ref(500);

  let storeCtag = usersStore.user.ctag;
  let loginname = usersStore.user.loginname;

  /**
   * read
   *
   * liest Daten aus
   *
   * @param string  target
   * @param integer ctag
   * @param integer page
   */
  async function read(target, ctag, page) {
    // page = "undefined" != typeof page ? page : 0;
    const default_param = {
      page: "undefined" != typeof page ? page : 0,
      page_size: page_size.value,
    };
    let params = ctag ? { ctag: ctag, ...default_param } : default_param;

    let buffer = JSON.parse(localStorage.getItem(target)) ?? {};
    let data;
    let err = null;

    // buffer = buffer ? buffer : {};

    do {
      data = await api
        .get(baseUrl + target, params)
        .then((data) => {
          return data;
        })
        .catch((error) => {
          err = { error };
        });

      // Falls Daten geladen werden konnten
      if (data?.length) {
        // Falls noch keine Daten aus LocalStorage ausgelesen werden konnten
        if (!buffer) {
          // Leeres Buffer-Objekt erstellen
          buffer = {};
        }

        // Daten als Objekt mit der Zaehler-Id speichern
        for (let i = 0; i < data.length; i++) {
          buffer[data[i]["id"]] = data[i];
          // if (data[i]["ctag"] * 1 > usersStore.user.ctag * 1) {
          if (data[i]["ctag"] * 1 > storeCtag * 1) {
            // console.log(data[i]);
            // console.log("storeCtag",data[i]["ctag"]);
            storeCtag = data[i]["ctag"];
          }
        }
        // Nächste Seite vormerken
        params.page++;

        // console.log(
        //   target +
        //     "-> " +
        //     storeCtag +
        //     " -> " +
        //     data.length +
        //     " === " +
        //     params.page_size
        // );
      }
    } while (data.length === params.page_size);

    usersStore.setCtag(storeCtag);
    // Daten im Localstorage speichern
    localStorage.setItem(target, JSON.stringify(buffer));

    // Daten der State-Variablen counter zuweisen
    return buffer;
  }

  /**
   * saveReadings
   *
   * sichert Zählerstände
   *
   * @param array reading
   */
  async function saveReadings(readings) {
    var result;

    for (let i in readings) {
      // if no reading ist present
      if (!readings[i].reading && !readings[i].consumption) {
        // remove counter
        delete readings[i];
      }
    }
    console.log("Readings:Value", readings);
    result = await api.post(`${baseUrl}readings`, readings);
    if (0 < result) {
      readcounters(usersStore.user.ctag);
      readreadings(usersStore.user.ctag);
      usersStore.setCtag(storeCtag);
    }
    console.log(result);
  }

  /**
   * saveCounter
   *
   * speichert den übermittelten Zähler
   *
   * @param array counter
   */
  async function saveCounter(counter) {
    var result;
    var touched = counter.touched;

    delete counter.touched;

    if (counter?.id) {
      result = await api.put(`${baseUrl}counters`, counter);
    } else {
      result = await api.post(`${baseUrl}counters`, counter);
    }

    console.log("Counter", result);
    // Wenn neues ctag
    if (result instanceof Array && result.length) {
      let row = result.shift();
      if (row.ctag && row.ctag * 1 > storeCtag * 1) {
        let old_readings = counters.value[row.id]?.readings;

        counters.value[row.id] = { ...row, readings: old_readings };

        // Falls es sich um einen neuen Zähler handelt
        if (!counter?.id) {
          if(!readings?.value?.counters){
            readings.value.counters = {};
          }
          // Eintrag in counters-key des readings-Objektes erstellen
          readings.value.counters[row.id] = [];
        }

        // Counter-Eintrag aus Local-Storage auslesen, ...
        let ls_counters = JSON.parse(localStorage.getItem("counters"));
        // ... anpassen ...
        ls_counters[row.id] = { ...row };

        // ... Werte encoden ...
        let counters_str = JSON.stringify({ ...ls_counters });
        // ... und speichern
        localStorage.setItem("counters", counters_str);

        // Falls keine zwischenzeitlichen Änderungen erfolgt sind
        if (row.ctag * 1 === storeCtag * 1 + 1) {
          storeCtag = row.ctag;
          // aktuelles ctag im User-Store sichern
          usersStore.setCtag(storeCtag);
        }
      }
      return row;
    } else {
      if (touched) {
        counter.touched = touched;
        return counter;
      }
    }
    // Wenn ctag genau um 1 höher ist
    // ctag anpassen
  }

  /**
   * saveReading
   *
   * speichert den übermittelten Zählerstand
   *
   * @param array reading
   */
  async function saveReading(reading) {
    var result;
    var touched = reading.touched;

    delete reading.touched;

    console.log("Reading", { ...reading });
    if (reading?.id) {
      result = await api.put(`${baseUrl}readings`, reading);
    } else {
      result = await api.post(`${baseUrl}readings`, reading);
    }

    console.log("Counter", result);
    // Wenn neues ctag
    if (result instanceof Array && result.length) {
      let row = result.shift();
      if (row.ctag && row.ctag * 1 > storeCtag * 1) {
        // Nicht benötigte Keys entfernen
        delete row.counterName;
        delete row.formattedDate;

        readings.value[row.id] = { ...row };

        // Die Einträge des keys "counters" zwischenspeichern
        let counters = readings?.value?.counters;

        // Key fürs JSON entfernen, weil dessen Einträge auf die
        // Zählerstände verweisen, was beim JSON.stringify einen Fehler erzeugt
        delete readings?.value?.counters;

        // Werte encoden
        let readings_str = JSON.stringify({ ...readings.value });
        // und speichern
        localStorage.setItem("readings", readings_str);

        console.log("Counters:", counters[row.counter_id].length);
        let i = 0;
        while (
          counters[row.counter_id][i] &&
          counters[row.counter_id][i].id != row.id &&
          i < counters[row.counter_id].length
        ) {
          i++;
        }
        if (i < counters[row.counter_id].length) {
          counters[row.counter_id][i] = row;
        } else {
          counters[row.counter_id].push(row);
        }
        let current_reading = counters[row.counter_id].find(function (row) {
          if (row.id === this) {
            return true;
          }
          return false;
        }, row.id);
        // current_reading.value = {...row};

        console.log("Counter", current_reading);
        // Eintrag "counters" wiederherstellen
        readings.value.counters = counters;

        // Falls keine zwischenzeitlichen Änderungen erfolgt sind
        if (row.ctag * 1 === storeCtag * 1 + 1) {
          storeCtag = row.ctag;
          // aktuelles ctag im User-Store sichern
          usersStore.setCtag(storeCtag);
        }
      }
      return row;
    } else {
      if (touched) {
        reading.touched = touched;
        return reading;
      }
    }
    // Wenn ctag genau um 1 höher ist
    // ctag anpassen
  }

  /**
   * getLastReading
   *
   * liest den ketzten Eintrag eines Zaehlers aus
   *
   * @param integer counter
   */
  function getLastReading(counter) {
    let result = null;

    if (readings?.value?.counters && counter in readings.value.counters) {
      let myReadings = readings.value.counters[counter];
      let myReading = myReadings[myReadings.length - 1];
      switch (counters?.value?.[counter]?.counter_type * 1) {
        case 2: {
          result = myReading?.consumption ?? 0;
          break;
        }
        case 1:
        default:
          result = myReading?.reading ?? 0;
      }
    } else {
      result = "";
    }
    // console.log(counter, counters?.value?.[counter], result);
    return result;
  }

  /**
   * readcounters
   *
   * liest die Daten der vorhandenen Zaehler aus
   *
   * @param integer ctag
   * @param integer page
   */
  async function readcounters(ctag, page) {
    var counter_types = [];
    var data = (await read("counters", ctag, page)) ?? {};
    console.log(data);
    counters.value = data ? data : {};
    // cTag darf noch nicht im usersStore gesichert werden
    // muss eventuell angepasst werden, falls diese Methode
    // nicht nur beim Initialisieren aufgerufen wird
    // usersStore.setCtag(storeCtag);
  }

  /**
   * readreadings
   *
   * liest die Daten der vorhandenen Zaehler aus
   *
   * @param integer ctag
   * @param integer page
   */
  async function readreadings(ctag, page) {
    let data = (await read("readings", ctag, page)) ?? {};
    for (let i in data) {
      let counter_id = data[i]["counter_id"];
      let reading_id = data[i]["id"];
      if (!("counters" in data)) {
        data.counters = {};
      }
      if (!(counter_id in data.counters)) {
        data.counters[counter_id] = [];
      }
      // data.counters[counter_id][reading_id] = data[i];
      data.counters[counter_id].push(data[i]);
    }
    readings.value = data ? data : {};
    loading.value = false;
    // console.log(getForm.value);
  }

  readcounters(usersStore.user.ctag);
  readreadings(usersStore.user.ctag);
  console.log(readings);
  usersStore.setCtag(storeCtag);
  // console.log(counters.value);
  // }

  return {
    counters,
    current,
    getForm,
    loading,
    measures,
    page,
    page_size,
    readings,
    saveCounter,
    saveReading,
    saveReadings,
    type,
  };
});

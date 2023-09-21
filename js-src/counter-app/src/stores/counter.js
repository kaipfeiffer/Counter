import { ref, computed } from "vue";
import { defineStore } from "pinia";
import { api } from "@/modules/auth-api";
import { useUserStore } from "@/stores/user";
import { setDecimalPlaces } from "@/modules/tools";

const baseUrl = `${import.meta.env.VITE_API_URL}`;

export const useCounterStore = defineStore("counter_store", () => {
  const usersStore = useUserStore();
  /**
   * exportierte State variablen
   */
  const readings = ref({});
  const counters = ref({});
  const current = ref({});
  const getForm = computed(() => {
    let dataRow = {};
    console.log("dataRow");
    for (let i in counters.value) {
      let counter = counters.value[i];
      dataRow[i] = {
        name: counter["name"],
        reading: setDecimalPlaces(getLastReading(i), 1, "always"),
        id: counter["id"],
      };
      current.value[counter["id"]] = null;
    }
    console.log(dataRow);
    return dataRow;
  });
  const loading = ref(true);
  const page = ref(0);
  const page_size = ref(500);

  let storeCtag = usersStore.user.ctag;

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

    let buffer = JSON.parse(localStorage.getItem(target));
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
          if (data[i]["ctag"] * 1 > usersStore.user.ctag * 1) {
            console.log(data[i]);
            console.log(data[i]["ctag"]);
            storeCtag = data[i]["ctag"];
          }
        }
        // Nächste Seite vormerken
        params.page++;

        console.log(
          target +
            "-> " +
            storeCtag +
            " -> " +
            data.length +
            " === " +
            params.page_size
        );
      }
    } while (data.length === params.page_size);

    // Daten im Localstorage speichern
    localStorage.setItem(target, JSON.stringify(buffer));

    // Daten der State-Variablen counter zuweisen
    return buffer;
  }

  /**
   * getLastReading
   *
   * liest den ketzten Eintrag eines Zaehlera aus
   *
   * @param array reading
   */
  async function saveReadings(readings) { 
    var result;

    console.log("Readings:Value",readings);
    result = await api.post(`${baseUrl}readings`, readings);
    console.log(result);
   }

  /**
   * getLastReading
   *
   * liest den ketzten Eintrag eines Zaehlera aus
   *
   * @param array reading
   */
  async function saveReading(reading) {
    var result;

    if (reading?.id) {
      result = await api.put(`${baseUrl}readings`, reading);
    } else {
      result = await api.post(`${baseUrl}readings`, reading);
    }

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
        delete (readings?.value?.counters);

        // Werte encoden
        let readings_str = JSON.stringify({...readings.value});
        // und speichern
        localStorage.setItem("readings", readings_str);

        // Eintrag "counters" wiederherstellen
        readings.value.counters = counters;

        // Falls der keine zwischenzeitlichen Änderungen erfolgt sind
        if (row.ctag * 1 === storeCtag * 1 + 1) {
          storeCtag = row.ctag;
          // aktuelles ctag im User-Store sichern
          usersStore.setCtag(storeCtag);
        }
      }
    }
    // Wenn ctag genau um 1 höher ist
    // ctag anpassen
  }

  /**
   * getLastReading
   *
   * liest den ketzten Eintrag eines Zaehlera aus
   *
   * @param integer counter
   */
  function getLastReading(counter) {
    let result = null;

    if (counter in readings.value.counters) {
      let myReadings = readings.value.counters[counter];
      let myReading = myReadings[myReadings.length - 1];
      result = myReading.reading;
    }
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
    counters.value = await read("counters", ctag, page);

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
    let data = await read("readings", ctag, page);
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
    readings.value = data;
    loading.value = false;
    usersStore.setCtag(storeCtag);
    // console.log(getForm.value);
  }

  // if (!counterBuffer) {
  //   console.log("loadCounters");
  //   readcounters();
  //   readreadings();
  // } else {
  readcounters(usersStore.user.ctag);
  readreadings(usersStore.user.ctag);
  console.log(counters.value);
  // }

  return {
    counters,
    current,
    getForm,
    loading,
    page,
    page_size,
    readings,
    saveReading,
    saveReadings,
  };
});

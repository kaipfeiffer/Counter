<script setup></script>

<template>
  <div>
    <header>
      <h2 v-if="counter && !action">
        <RouterLink
          :to="{ name: 'counter', params: { action: 'edit', id: counter.id } }"
          >{{ counter.counter_name }}</RouterLink
        >
      </h2>
      <div
        v-if="counter && !action"
        :class="'yearly' === displayedType ? 'selected' : ''"
      >
        <RouterLink
          :to="{
            name: 'counterinfo',
            params: { type: 'yearly', id: counter.id },
          }"
          @click="
            // console.log('Clicked Year');
            setType('yearly');
          "
          >Jahr</RouterLink
        >
      </div>
      <div
        v-if="counter && !action"
        :class="'monthly' === displayedType ? 'selected' : ''"
      >
        <RouterLink
          :to="{
            name: 'counterinfo',
            params: { type: 'monthly', id: counter.id },
          }"
          @click="
            // console.log('Clicked month');
            setType('monthly');
          "
          >Monat</RouterLink
        >
      </div>
      <div
        v-if="counter && !action"
        :class="'weekly' === displayedType ? 'selected' : ''"
      >
        <RouterLink
          :to="{
            name: 'counterinfo',
            params: { type: 'weekly', id: counter.id },
          }"
          @click="
            // console.log('Clicked week');
            setType('weekly');
          "
          >Woche</RouterLink
        >
      </div>
      <div
        v-if="counter && !action"
        :class="'daily' === displayedType ? 'selected' : ''"
      >
        <RouterLink
          :to="{
            name: 'counterinfo',
            params: { type: 'daily', id: counter.id },
          }"
          @click="
            // console.log('Clicked daily');
            setType('daily');
          "
          >Tag</RouterLink
        >
      </div>
    </header>

    <div class="counter-view" v-if="!loading && !action && counter">
      <SortableTable :rows="getReadings" :head="settings"> </SortableTable>
    </div>
    <div v-if="loading" class="spinner-border spinner-border-sm">
      Lade Daten
    </div>
  </div>
</template>
<script>
import { RouterLink, useRoute } from "vue-router";
import { computed, defineComponent, onMounted, ref } from "vue";
import { useCounterStore } from "@/stores/counter";
import {
  formatDate,
  getCalendarWeek,
  setDecimalPlaces,
  createDateObject,
} from "@/modules/tools";
import SortableTable from "@/components/tools/SortableTable.vue";
import UniversalForm from "@/components/tools/UniversalForm.vue";

export default defineComponent({
  components: { RouterLink, SortableTable, UniversalForm },
  setup: () => {
    const monthNames = [
      { name: "Januar", shortname: "Jan", days: 31 },
      { name: "Febuar", shortname: "Feb", days: 29 },
      { name: "März", shortname: "Mär", days: 31 },
      { name: "April", shortname: "Apr", days: 30 },
      { name: "Mai", shortname: "Mai", days: 31 },
      { name: "Juni", shortname: "Jun", days: 30 },
      { name: "Juli", shortname: "Jul", days: 31 },
      { name: "August", shortname: "Aug", days: 31 },
      { name: "September", shortname: "Sep", days: 30 },
      { name: "Oktober", shortname: "Okt", days: 31 },
      { name: "November", shortname: "Nov", days: 30 },
      { name: "Dezember", shortname: "Dez", days: 31 },
    ];
    const route = useRoute();
    const action = computed(() => {
      if (0 <= ["create", "edit"].indexOf(route.params.action)) {
        return "edit";
      }
      return null;
    });
    const counterStore = useCounterStore();
    const counter = computed(() => {
      let counter = getCounter(route.params.id);
      return counter;
    });

    const detailData = ref({});

    const displayedType = ref("");

    const displayedYear = ref(0);

    const settings = computed(() => {
      if (displayedYear.value) {
        let displayedYearsCnt = 5;
        const titles = {
          yearly: "Jahr",
          monthly: "Monat",
          weekly: "KW",
          daily: "Datum",
        };
        let settings = [
          { title: titles[displayedType.value] ?? "Index", column: "row" },
        ];
        let years = prepareData();
        let currentYear =
          displayedYear.value + Math.floor(displayedYearsCnt / 2);

        // console.log(years);
        while (!years[currentYear] && currentYear * 1 > 2010) {
          currentYear--;
        }

        // check if the target year has data
        while (!years[currentYear - displayedYearsCnt]) {
          displayedYearsCnt--;
        }

        // if only the current Year has data, set count to "1"
        displayedYearsCnt = displayedYearsCnt ? displayedYearsCnt : 1;

        currentYear -= displayedYearsCnt - 1;
        if ("yearly" !== displayedType.value) {
          for (let i = 1; i <= displayedYearsCnt; i++) {
            settings.push({
              title: currentYear,
              column: "year" + currentYear,
            });
            currentYear++;
          }
        } else {
          settings.push({
            title: "Verbrauch",
            column: "consumption",
          });
        }
        // console.log(settings);
        return settings;
      }
    });

    const setType = (type) => {
      displayedType.value = type;
    };

    const getReadings = computed(() => {
      let readings = [];
      if (displayedYear.value) {
        let displayedYearsCnt = 5;
        let years = prepareData();
        if (years) {
          let currentYear =
            displayedYear.value + Math.floor(displayedYearsCnt / 2);
          let entryCnt = 0;
          let entryIndex = {};

          // console.log(years, displayedType.value);
          while (!years[currentYear]) {
            currentYear--;
          }

          // check if the target year has data
          while (!years[currentYear - displayedYearsCnt]) {
            displayedYearsCnt--;
          }

          displayedYearsCnt = displayedYearsCnt ? displayedYearsCnt : 1;

          let startYear = currentYear - displayedYearsCnt + 1;

          currentYear = startYear;

          // console.log(currentYear);
          switch (displayedType.value) {
            case "monthly":
            case "weekly": {
              // Überprüfen, welches Jahr die meisten Einträge hat
              // Alle Jahre durchgehen
              for (let i = 1; i <= displayedYearsCnt; i++) {
                let keys;
                switch (displayedType.value) {
                  case "weekly": {
                    keys = Object.keys(years[currentYear].weeks);
                    break;
                  }
                  case "monthly":
                  default: {
                    keys = Object.keys(years[currentYear].months);
                  }
                }
                if (keys.length > entryCnt) {
                  entryCnt = keys.length;
                  entryIndex = keys;
                }
                // console.log(currentYear,years[currentYear],years[currentYear].months,Object.keys(years[currentYear].months) );
                currentYear++;
              }
              // console.log(entryIndex);
              // Alle Einträge durchgehen
              for (let key of entryIndex) {
                // Alle Jahre durchgehen
                let row = { row: key };
                currentYear = startYear;

                for (let i = 1; i <= displayedYearsCnt; i++) {
                  let val;
                  switch (displayedType.value) {
                    case "weekly": {
                      val = years[currentYear].weeks?.[key]?.consumption ?? "";
                      break;
                    }
                    case "monthly":
                    default: {
                      val = years[currentYear].months?.[key]?.consumption ?? "";
                    }
                  }
                  row["year" + currentYear] = val;
                  currentYear++;
                }
                readings.push(row);
              }
              break;
            }

            case "daily": {
              for (let i = 0; i < monthNames.length; i++) {
                let daysInMonth = monthNames[i].days;
                let currentMonthName = monthNames[i].shortname;
                readings.push({ row: currentMonthName });
                for (let d = 1; d <= daysInMonth; d++) {
                  let row = { row: d + "." + currentMonthName };
                  currentYear = startYear;
                  for (let i = 1; i <= displayedYearsCnt; i++) {
                    let className =
                      years[currentYear].months?.[currentMonthName]?.days[d]
                        ?.class ?? "";
                    let val =
                      years[currentYear].months?.[currentMonthName]?.days[d]
                        ?.consumption ?? "";
                    val = val ? setDecimalPlaces(val) : "";
                    row["year" + currentYear] = {
                      value: val,
                      class: className,
                    };
                    currentYear++;
                  }
                  readings.push(row);
                }
              }
              break;
            }

            case "yearly":
            default: {
              for (let year in years) {
                let row = { row: year, consumption: years[year].consumption };
                readings.push(row);
              }
            }
          }
        }
      }
      // console.log(readings);
      return readings;
    });

    const prepareData = () => {
      const parseDate = (currentDate, dailyConsumption) => {
        // console.log(
        //   currentDate.toISOString(),
        //   currentDate.toLocaleString()
        //   // previousDate.toLocaleString()
        // );

        let currentYear = currentDate.getUTCFullYear();
        let currentMonth = currentDate.getUTCMonth();
        let currentMonthName = monthNames[currentMonth].shortname;
        let currentDay = currentDate.getUTCDate();
        let currentDayOfWeek = currentDate.getUTCDay();
        let currentWeek = getCalendarWeek(currentDate);

        console.log(
          "after",
          currentYear,
          currentMonth,
          currentDay,
          currentDayOfWeek,
          currentWeek,
          dailyConsumption
          // previousDate.toLocaleString()
        );
        // Jahreswerte
        if (!years[currentYear]) {
          years[currentYear] = {
            consumption: 0,
            class: "",
            months: {},
            weeks: {},
          };
        }
        years[currentYear].consumption =
          Math.round(
            (years[currentYear].consumption + dailyConsumption) * 100
          ) / 100;

        // Monatswerte
        if (!years[currentYear].months[currentMonthName]) {
          years[currentYear].months[currentMonthName] = {
            consumption: 0,
            class: "",
            days: {},
          };
        }
        years[currentYear].months[currentMonthName].consumption =
          Math.round(
            (years[currentYear].months[currentMonthName].consumption +
              dailyConsumption) *
              100
          ) / 100;

        // Wochenwerte
        if (!years[currentYear].weeks[currentWeek]) {
          years[currentYear].weeks[currentWeek] = {
            consumption: 0,
            class: "",
          };
        }
        years[currentYear].weeks[currentWeek].consumption =
          Math.round(
            (years[currentYear].weeks[currentWeek].consumption +
              dailyConsumption) *
              100
          ) / 100;

        // Tageswerte
        if (!years[currentYear].months[currentMonthName].days[currentDay]) {
          years[currentYear].months[currentMonthName].days[currentDay] = {
            consumption: dailyConsumption,
            class: "weekday_" + currentDayOfWeek,
          };
        }
      };
      var years = {};
      if (counter?.value?.id) {
        let storageCounterDetails = JSON.parse(
          localStorage.getItem("CounterDetails_" + counter.value.id)
        );
        years = storageCounterDetails?.years
          ? storageCounterDetails?.years
          : {};
        let reading = {};
        let previousDate = null;
        let previousReading = null;
        // console.log(storageCounterDetails?.ctag, "Text", counter.value.ctag);

        if (
          !storageCounterDetails ||
          storageCounterDetails?.ctag < counter.value.ctag
        ) {
          years = {};
          for (let i = 0; i < counter.value.readings.length; i++) {
            reading = { ...counter.value.readings[i] };

            // Falls es einen vorhergehenden Eintrag gibt
            if (previousDate) {
              // console.log("Datum", reading.date);
              // let currentDate = new Date(
              //   reading.date.replace(/\s[\d\:]+$/, "")
              // );
              let currentDate = createDateObject(reading.reading_date);

              // console.log(
              //   "Datum",
              //   currentDate.toISOString(),
              //   currentDate.toLocaleString()
              // );
              let timeInterval = Math.floor(
                (currentDate.getTime() - previousDate.getTime()) /
                  (3600000 * 24)
              );
              let dailyConsumption =
                Math.round(
                  (reading.consumption
                    ? reading.consumption / timeInterval
                    : reading.reading - previousReading) * 100
                ) / 100;

              // Jeden Tag des Intervalles durchgehen
              while (
                currentDate.getTime() > previousDate.getTime() &&
                timeInterval--
              ) {
                // console.log(
                //   currentDate.toISOString(),
                //   timeInterval,
                //   currentDate.getUTCFullYear(),
                //   currentDate.getUTCMonth(),
                //   currentDate.getUTCDate() - 1
                // );
                let tempDate = new Date(currentDate.toISOString());
                tempDate.setUTCMinutes(
                  tempDate.getUTCMinutes() - tempDate.getTimezoneOffset()
                );
                parseDate(tempDate, dailyConsumption);
                // parseDate(currentDate, dailyConsumption);

                // currentDate = new Date(
                //   currentYear,
                //   currentMonth,
                //   currentDay - 1
                // );
                // currentDate = new Date(
                //   currentDate.getUTCFullYear(),
                //   currentDate.getUTCMonth(),
                //   currentDate.getUTCDate() - 1,
                //   12,
                //   0,
                //   0
                // );
                // currentDate.setUTCMinutes(
                //   currentDate.getUTCMinutes() + currentDate.getTimezoneOffset()
                // );
                currentDate.setUTCDate(currentDate.getUTCDate() - 1);
                // console.log(
                //   currentDate.toLocaleString(),
                //   timeInterval,
                //   currentDate.getUTCFullYear(),
                //   currentDate.getUTCMonth(),
                //   currentDate.getUTCDate()
                // );
              }
            } else {
              // Verbrauch des ersten Eintrages dem passenden Yahr etc zu ordenen, dafür
              // den Inhalt der if-Bedingung in separate Function auslagern
              // let currentDate = new Date(
              //   reading.date.replace(/\s[\d\:]+$/, "")
              // );

              let currentDate = createDateObject(reading.reading_date);
              // console.log(
              //   "1stDatum",
              //   currentDate.toISOString(),
              //   currentDate.toLocaleString()
              // );
              let dailyConsumption = reading.consumption;
              let tempDate = new Date(currentDate.toISOString());
              tempDate.setUTCMinutes(
                tempDate.getUTCMinutes() - tempDate.getTimezoneOffset()
              );
              parseDate(tempDate, dailyConsumption);
              // parseDate(currentDate, dailyConsumption);
            }

            // aktuellen wert als vorhergehenden Wert festlegen
            previousDate = createDateObject(reading.reading_date);
            // previousDate = new Date(reading.date.replace(/\s[\d\:]+$/, ""));

            previousReading = reading.reading;
          }
          storageCounterDetails = { years: years, ctag: counter.value.ctag };
          localStorage.setItem(
            "CounterDetails_" + counter.value.id,
            JSON.stringify(storageCounterDetails)
          );
        }
      }
      // console.log(years);
      return years;
    };

    const loading = computed(() => counterStore.loading);

    function getCounter(id) {
      let counter = null;
      if (counterStore?.counters?.[id]) {
        counter = counterStore?.counters?.[id];
        counter.readings = counterStore?.readings?.counters?.[id];
      }
      return counter;
    }

    onMounted(() => {
      const id = route.params.id;
      displayedType.value = route.params.type ? route.params.type : "weekly";
      displayedYear.value = route.params.year
        ? route.params.year
        : new Date().getUTCFullYear();
      // prepareData();
      // action.value = route.params.action ? route.params.action  : null;
    });

    return {
      action,
      counter,
      displayedType,
      loading,
      getReadings,
      settings,
      setType,
    };
  },
  watch: {
    settings: {
      deep: true,
      handler: function (newValue, oldValue) {
        // If name or page updates, then we will be able to see it in our
        // newValue variable
        console.log("Watcher: settings", newValue, oldValue);
      },
    },
  },
});
</script>
<style scoped>
header {
  display: flex;
  align-items: center;
}

h2 {
  width: 28%;
}

header div {
  width: 18%;
  text-align: right;
  height: fit-content;
}

header div.selected {
  color: var(--color-menu-link-hover);
}

header a {
  font-size: 1em;
  padding-left: 50px;
}

.counter-view .right {
  text-align: right;
  display: inline-block;
  width: 80%;
  padding: 5px 10px;
  margin: 0 auto;
}

@media (min-width: 414px) {
  header a {
    font-size: 1.25em;
  }
}

@media (min-width: 768px) {
  header a {
    font-size: 1.5em;
  }
}
</style>
<style>
td.weekday_0 {
  background-color: #ccc;
}

td.weekday_6 {
  background-color: #ddd;
}
</style>

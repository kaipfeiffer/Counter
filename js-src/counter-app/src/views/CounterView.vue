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
      <h2 v-if="!loading && entry && 'edit' == action">
        <span v-if="!entry.counter_name">Neuen Zähler eingeben</span>{{ entry.counter_name }}
      </h2>
      <div v-if="!action">
        <RouterLink
          :to="{
            name: 'counterinfo',
            params: { type: 'yearly', id: counter.id },
          }"
          >&#x24D8;</RouterLink
        >
      </div>
      <div v-if="!action">
        <RouterLink
          :to="{
            name: 'reading',
            params: { action: 'create', counter_id: counter?.id },
          }"
          >+</RouterLink
        >
      </div>
    </header>

    <div class="counter-view" v-if="!loading && 'edit' == action">
      <UniversalForm
        :data="entry"
        :settings="settings"
        @submit-form="submitForm"
        :abort="{ name: 'counter', params: { id: entry.id } }"
      ></UniversalForm>
    </div>

    <div class="counter-view" v-if="!loading && !action && counter">
      <SortableTable :rows="getReadings" :head="tableHead"> </SortableTable>
    </div>
    <div v-if="loading" class="spinner-border spinner-border-sm">
      Lade Daten
    </div>
  </div>
</template>
<script>
import { RouterLink, useRoute, useRouter } from "vue-router";
import { computed, defineComponent, onMounted, ref } from "vue";
import { useCounterStore } from "@/stores/counter";
import { formatDate } from "@/modules/tools";
import SortableTable from "@/components/tools/SortableTable.vue";
import UniversalForm from "@/components/tools/UniversalForm.vue";

export default defineComponent({
  components: { RouterLink, SortableTable, UniversalForm },
  setup: () => {
    const route = useRoute();
    const router = useRouter();
    const action = computed(() => {
      if (0 <= ["create", "edit"].indexOf(route.params.action)) {
        return "edit";
      }
      return null;
    });
    const counterStore = useCounterStore();
    const counter = computed(() => {
      let counter = getCounter(route.params.id);
      // console.log(counter);
      return counter;
    });

    const settings = {
      attributes: {
        reading: { step: 0.1 },
        measure: { options: counterStore.measures },
      },
      input_formatters: {},
      labels: {
        counter_name: "Name",
        measure: "Maß-Einheit",
        // counter_location: "Ort",
        identifier: "Zählernummer",
      },
      output_formatters: {},
      writable: {
        counter_name: "text",
        measure: "select",
        // counter_location: "text",
        identifier: "text",
        counter_type: "hidden",
      },
    };

    const tableHead = computed(() => {
      let head;
      // console.log("type",counter.value.counter_type);
      switch (counter.value.counter_type * 1) {
        case 2: {
          head = [
            {
              title: null,
              links: [{ title: "bearbeiten", route: "reading", index: "id" }],
            },
            { title: "Datum", column: "reading_date" },
            {
              title: "Zählerstand",
              column: "reading",
              class: "right",
            },
            {
              title: "Verbrauch",
              column: "consumption",
              class: "right",
            },
            {
              title: "Einheit",
              column: "measure",
              class: "left",
            },
          ];
          break;
        }
        case 1:
        default: {
          head = [
            {
              title: null,
              links: [{ title: "bearbeiten", route: "reading", index: "id" }],
            },
            { title: "Datum", column: "reading_date" },
            {
              title: "Zählerstand",
              column: "reading",
              class: "right",
            },
            {
              title: "Einheit",
              column: "measure",
              class: "left",
            },
            {
              title: "Verbrauch",
              column: "consumption",
              class: "right",
            },
            {
              title: "Einheit",
              column: "measure",
              class: "left",
            },
          ];
          break;
        }
      }

      return head;
    });

    let entry = computed({
      get: () => {
        let counter = { ...getDefault(), ...getCounter(route.params.id, true) };
        let formatters = settings.output_formatters;

        for (let i in counter) {
          counter[i] = formatters[i] ? formatters[i](counter[i]) : counter[i];
        }
        // console.log("entry", counter);
        return counter;
      },
      set: (newValue) => {
        // console.log(newValue);
        let formatters = settings.input_formatters;
        for (let i in newValue) {
          newValue[i] = formatters[i]
            ? formatters[i](newValue[i])
            : newValue[i];
        }
        entry.value = newValue;
      },
    });

    function getDefault() {
      let counter = { ...settings.writable };

      for (let i in counter) {
        counter[i] = "";
      }
      counter.counter_type = route.params.type;
      // console.log("counter-default", counter);
      return counter;
    }

    const submitForm = (id) => {
      // console.log("submitForm", entry);
      counterStore.saveCounter(entry.value);
      router.push({
        name: "home",
      });
    };

    const getReadings = computed(() => {
      let readings = [];
      let reading = {};
      const measure = counter.value.measure
        ? counterStore.measures[counter.value.measure]
        : "";
      if (counter?.value?.readings) {
        // console.log(counter.value.readings);
        for (let i = 0; i < counter.value.readings.length; i++) {
          reading = { ...counter.value.readings[i] };
          // console.log(readings);
          reading.consumption = (reading.consumption * 1).toLocaleString(
            undefined,
            {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2,
            }
          );
          reading.reading = (reading.reading * 1).toLocaleString(
            undefined,
            {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2,
            }
          );
          reading.measure = measure;
          reading.reading_date = formatDate(reading.reading_date, "d.m.Y");
          readings.push(reading);
        }
        // console.log(readings);
      }
      return readings;
    });

    const loading = computed(() => counterStore.loading);

    function getCounter(id, edit) {
      let counter = null;

      // console.log(id, edit);

      if (counterStore?.counters?.[id]) {
        counter = counterStore?.counters?.[id];
        if (!edit) {
          counter.readings = counterStore?.readings?.counters?.[id];
        }
      }
      // console.log("counter", counter);
      return counter;
    }

    onMounted(() => {
      const id = route.params.id;
      // action.value = route.params.action ? route.params.action  : null;
    });

    return {
      action,
      entry,
      counter,
      loading,
      getReadings,
      settings,
      submitForm,
      tableHead,
    };
  },
  watch: {
    counter: {
      deep: true,
      handler: function (newValue, oldValue) {
        // If name or page updates, then we will be able to see it in our
        // newValue variable
        console.log("Watcher: counter", newValue, oldValue);
      },
    },
  },
});
</script>
<style>

.counter-view .right {
  text-align: right;
  display: inline-block;
  width: 80%;
  padding: 5px 10px;
  margin: 0 auto;
}
.counter-view .left {
  text-align: left;
  display: inline-block;
  width: 80%;
  padding: 5px 10px;
  margin: 0 auto;
}
</style>
<style scoped>
header {
  display: flex;
}

h2 {
  width: 70%;
}

header div {
  width: 30%;
  text-align: right;
  font-size: 1.5em;
}

header a {
  font-size: 1.5em;
  padding-left: 50px;
}
.counter-view {
  border: 1px solid var(--color-border);
  padding: 10px;
}
</style>

<script setup></script>

<template>
  <header>
    <h2 v-if="entry && entry.id">
      {{ entry.counterName }} {{ entry.formattedDate }}
    </h2>
    <h2 v-if="entry && !entry.id">
      Neuen Eintrag für {{ entry.counterName }} erstellen
    </h2>
  </header>
  <div class="reading-view">
    <div class="content" v-if="!loading && entry">
      <UniversalForm
        :data="entry"
        :settings="settings"
        @submit-form="submitForm"
        :abort="{ name: 'counter', params: { id: entry.counter_id } }"
      ></UniversalForm>
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
import { formatDate, setDecimalPlaces } from "@/modules/tools";
import UniversalForm from "@/components/tools/UniversalForm.vue";

export default defineComponent({
  components: { RouterLink, UniversalForm },
  setup: () => {
    const route = useRoute();
    const router = useRouter();
    const counterStore = useCounterStore();

    const settings = computed(() => {
      let settings = {
        attributes: {
          reading: { step: 0.1 },
          consumption: { step: 0.1 },
        },
        input_formatters: {
          date: (val) => {
            if (
              val.match(
                /(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2})(?::(\d{2})){0,1}/
              )
            ) {
              return val.replace("T", " ");
            }
          },
        },
        labels: {
          counterName: "Zähler",
          date: "Datum",
          reading: "Zählerstand",
          consumption: "Verbrauch",
          temperature: "Temperatur",
          remark: "Bemerkungen",
        },
        output_formatters: {
          reading: (val) => {
            return setDecimalPlaces(val, 1);
          },
          consumption: (val) => {
            return setDecimalPlaces(val, 1);
          },
          date: (val) => {
            return val ? formatDate(val, "Y-m-dTh:i") : "";
          },
        },
        writable: {
          reading: "decimal",
          consumption: "decimal",
          temperature: "number",
          date: "datetime-local",
          remark: "textarea",
        },
      };
      return settings;
    });

    let entry = computed({
      get: () => {
        let counter = { ...getDefault(), ...getReading(route.params.id) };
        let formatters = settings.value.output_formatters;

        for (let i in counter) {
          counter[i] = formatters[i] ? formatters[i](counter[i]) : counter[i];
        }
        return counter;
      },
      set: (newValue) => {
        let formatters = settings.value.input_formatters;
        for (let i in newValue) {
          newValue[i] = formatters[i]
            ? formatters[i](newValue[i])
            : newValue[i];
        }
        entry.value = newValue;
      },
    });

    const loading = computed(() => counterStore.loading);

    function getDefault() {
      let counter = null;
      let reading = { ...settings.value.writable };
      let date = new Date();
      console.log(date, date.getMinutes() + -1 * date.getTimezoneOffset());
      date.setMinutes(date.getMinutes() + -1 * date.getTimezoneOffset());

      for (let i in reading) {
        reading[i] = "";
      }
      console.log("ROUTE", route.params);
      reading.counter_id = route.params.counter_id;
      counter = counterStore?.counters?.[reading.counter_id];
      reading.counterName = counter?.name;
      console.log(date.getTimezoneOffset());
      reading.date = date.toISOString().replace("T", " ").replace("Z", "");
      return reading;
    }

    function getReading(id) {
      let reading = null;
      let counter = null;

      if (id && counterStore?.readings?.[id]) {
        reading = counterStore?.readings?.[id];
        reading.formattedDate = formatDate(reading.date, "d.m.Y");
        counter = counterStore?.counters?.[reading.counter_id];
        reading.counterName = counter?.name;
      }
      // console.log(reading);
      return reading;
    }

    async function submitForm() {
      console.log("submit", entry.value);
      counterStore.saveReading(entry.value);
      router.push({ 
        name: 'counter', 
        params: { id: entry.value.counter_id } });
    }

    onMounted(() => {
      const id = route.params.id;
    });

    console.log(entry);
    return {
      entry,
      loading,
      settings,
      submitForm,
    };
  },
  watch: {},
});
</script>
<style scoped>
header,
div.content {
  background-color: var(--color-background);
}

div.submit-div {
  padding-top: 20px;
  text-align: right;
}

div.content {
  padding: 0 20px 20px 20px;
}

.reading-view {
  border: 1px solid var(--color-border);
  padding: 10px;
}

textarea,
input {
  padding: 5px;
  width: 100%;
}

@media (min-width: 376px) {
  dt {
    float: left;
    width: 130px;
  }

  dd,
  dt {
    padding: 5px 5px 0 5px;
  }

  dd {
    margin-left: 140px;
    width: 1500px;
  }

  textarea,
  input {
    width: 150px;
  }
}
</style>

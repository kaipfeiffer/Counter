<script setup>
</script>

<template>
  <div class="back-navigation" v-if="entry">
    <RouterLink class="button" :to="{ name: 'counter', params: { id: entry.counter_id } }">zurück</RouterLink>
  </div>
  <div class="reading-view">
    <header>
      <h2 v-if="entry">
        {{ entry.counterName }} {{ entry.formattedDate }}
      </h2>
    </header>
    <div class="content" v-if="!loading && entry">
      <form @submit.prevent="submitForm">
        <dl>
          <dt>
            <label for="reading_date">
              Datum:
            </label>
          </dt>
          <dd>
            <input id="reading_date" type="datetime-local" v-model="date" />
          </dd>
          <dt>
            <label for="reading_reading">
              Zähler&shy;stand:
            </label>
          </dt>
          <dd>
            <input id="reading_reading" type="number" step="0.1" v-model="reading" />
          </dd>
          <dt>
            <label for="reading_temperature">
              Temperatur:
            </label>
          </dt>
          <dd>
            <input id="reading_temperature" type="number" step="1" v-model="temperature" />
          </dd>
          <dt>
            <label for="reading_remark">
              Bemerkungen:
            </label>
          </dt>
          <dd>
            <textarea id="reading_remark" v-model="remark">
            </textarea>
          </dd>
        </dl>
        <div class="submit-div">
          <input type="submit" value="Änderungen speichern" />
        </div>
      </form>
    </div>
    <div v-if="loading" class="spinner-border spinner-border-sm">Lade Daten</div>
  </div>
</template>
<script>
import { RouterLink, useRoute } from 'vue-router';
import { computed, defineComponent, onMounted, ref } from "vue";
import { useCounterStore } from "@/stores/counter";
import { formatDate, setDecimalPlaces } from "@/modules/tools";
import UniversalForm from "@/components/tools/UniversalForm.vue";

export default defineComponent({
  components: { RouterLink, UniversalForm },
  setup: () => {
    const route = useRoute();
    const counterStore = useCounterStore();
    let entry = computed({
      get: () => {
        let counter = { ...getReading(route.params.id) };
        return counter;
      },
      set: (newValue) => {
        entry.value = newValue
      }
    });

    // console.log(route.params.id)

    const reading = computed({
      get: () => {
        if (entry) {
          return setDecimalPlaces(entry.value.reading, 1);
        }
      },
      set: (newValue) => {
        entry.value.reading = newValue
      }
    })
    const date = computed({
      get: () => {
        // console.log(entry)
        if (entry) {
          return formatDate(entry.value.date, 'Y-m-dTh:i');
        }
      },
      set: (newValue) => {
        if (newValue.match(/(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2})(?::(\d{2})){0,1}/)) {
          entry.value.date = newValue.replace("T", " ");
        }
      }
    })
    const temperature = computed({
      get: () => {
        // console.log(entry)
        if (entry) {
          return entry.value.temperature;
        }
      },
      set: (newValue) => {
        entry.value.temperature = newValue
      }
    })
    const remark = computed({
      get: () => {
        // console.log(entry)
        if (entry) {
          return entry.value.remark;
        }
      },
      set: (newValue) => {
        entry.value.remark = newValue
      }
    })

    const settings = computed(() => {
      return {
        writable: {
          reading: "decimal2",
          temperature: "decimal2",
          date: "date",
          remark: "textarea",
        },
        labels: {
          counterName: "Zähler",
          date: "Datum",
          reading: "Zaehlerstand",
          temperature: "Temperatur",
          remark: "Bemerkungen"
        }
      };
    });

    const loading = computed(() => counterStore.loading)

    function getReading(id) {
      let reading = null;
      let counter = null;

      if (counterStore?.readings?.[id]) {
        reading = counterStore?.readings?.[id];
        reading.formattedDate = formatDate(reading.date, "d.m.Y")
        counter = counterStore?.counters?.[reading.counter_id];
        reading.counterName = counter?.name;
      }
      // console.log(reading);
      return reading
    }

    async function submitForm() {
      // console.log("submit", entry.value);
      counterStore.saveReading(entry.value);
    }

    onMounted(() => {
      const id = route.params.id
    })

    return {
      date, entry, loading, reading, settings, remark, submitForm, temperature,
    }
  },
  watch: {
  }
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

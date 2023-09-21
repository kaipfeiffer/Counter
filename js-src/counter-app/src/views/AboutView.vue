<script>
</script>

<template>
  <div>
    <form @submit.prevent="submitForm">
      <table v-if="!loading">
        <thead>
          <tr>
            <th>
              Zählername:
            </th>
            <th>
              Zaehlerstand:
            </th>
            <th>
              Neu:
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="counter in getForm">
            <td>
              <RouterLink :to="{ name: 'counter', params: { id: counter.id } }">{{ counter.name }}</RouterLink>
            </td>
            <td class="right">
              {{ counter.reading }}
            </td>
            <td>
              <input type="number" step="0.1" v-model="current[counter.id]" />
            </td>
          </tr>
        </tbody>
      </table>
      <div class="submit-div">
        <input type="submit" value="Einträge speichern" />
      </div>
    </form>
    <div v-if="loading" class="spinner-border spinner-border-sm">Lade Daten</div>
    <div v-if="counters && counters.error" class="text-danger">Error loading users: {{ counters?.error }}</div>
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

    const counters  = computed(() => counterStore.counters)    
    const current = computed({
      get:() => {return counterStore.current},
      set:(newVal) => {
        console.log("NewVal",newVal);
        counterStore.current.value = newVal;
      }})    
    const getForm = computed(() => counterStore.getForm)     
    const loading = computed(() => counterStore.loading)

    async function submitForm() {
      let data  = [];
      // Werte in normales Array mit den Einträgen als assoziatives Array, damit später Temperatur oder Bemerkungen
      // mit übertragen werden können.
      for(let i in current.value){
        data.push({counter_id:i,reading: current.value[i]});
      }
      counterStore.saveReadings(data);
    }

    onMounted(() => {
      const id = route.params.id
    })

    console.log("Current",current);
    return {
      counters, current, getForm,
      loading, submitForm
    }
  },
  watch: {
    loading: {
      deep: true,
      handler: function (newValue, oldValue) {
        // If name or page updates, then we will be able to see it in our
        // newValue variable
        console.log("Watcher: loading", newValue, oldValue)
      }
    }
  }
});
</script>
<style scoped>
.right {
  text-align: right;
  padding: 5px 10px;
}

input[type=number] {
  width: 100px;
  padding: 3px 5px;
}

.submit-div {
  text-align: right;
}
</style>

<script>
</script>

<template>
  <div>
    <header>
      <h2>
        Übersicht
      </h2>
      <div>
        <RouterLink :to="{ name: 'counter', params: { id: '', action: 'create', type: type } }">+</RouterLink>
      </div>
    </header>
    <div class="content">
      <div class="counter_types">
        <div v-for="(variant_type, key) in variants" @click="selectType(key)" :class="{ active: variant_type.active }">
          {{ variant_type.label }}
        </div>
      </div>
      <form @submit.prevent="submitForm">
        <table v-if="!loading">
          <thead>
            <tr>
              <th>
                Zählername:
              </th>
              <th v-if="1 === parseInt(type)" colspan="2">
                Zählerstand:
              </th>
              <th v-if="2 === parseInt(type)" colspan="2">
                Letzte Füllung:
              </th>
              <th colspan="2">
                Neu:
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="counter in getForm">
              <td>
                <RouterLink :to="{ name: 'counter', params: { id: counter.id } }">{{ counter.name }}</RouterLink>
              </td>
              <td v-if="1 === parseInt(type)" class="right">
                {{ counter.reading }}
              </td>
              <td v-if="2 === parseInt(type)" class="right">
                {{ counter.consumption }}
              </td>
              <td class="left">
                {{ counter.measure }}
              </td>
              <td class="right">
                <input type="number" step="0.1" v-model="current[counter.id]" />
              </td>
              <td class="left">
                {{ counter.measure }}
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
  </div>
</template>
<script>
import { RouterLink, useRoute } from 'vue-router';
import { computed, defineComponent, onMounted, ref } from "vue";
import { useCounterStore } from "@/stores/counter";
import { formatDate, setDecimalPlaces } from "@/modules/tools";
import UniversalForm from "@/components/tools/UniversalForm.vue";

export default defineComponent({
  components: { RouterLink, UniversalForm  },
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
    const getForm   = computed(() => counterStore.getForm)     
    const loading   = computed(() => counterStore.loading)
    const variants  = ref({1:{label:"Zählerstände",active: false},2:{label:"Tanken",active: false}});
    const type      = computed({
      get:() => {return counterStore.type},
      set:(newVal) => {
        counterStore.type = newVal;
      }}) 

    async function submitForm() {
      let data  = [];
      // Werte in normales Array mit den Einträgen als assoziatives Array, damit später Temperatur oder Bemerkungen
      // mit übertragen werden können.
      // console.log(current.value)
      for(let i in current.value){
        if(current.value[i]){
          switch(parseInt(type.value)){
            case 1:{
      // console.log(current.value)
              data.push({counter_id:i,reading: current.value[i]});
              break;
            }
            case 2:{
              data.push({counter_id:i,consumption: current.value[i]});
              break;
            }
          }
        }
      }
      console.log(current.value, ...data)
      counterStore.saveReadings(data);
    }

    function selectType(newValue){
      type.value = parseInt(newValue)
      for(let i in variants.value){
        // console.log(type.value,parseInt(i),parseInt(type.value) !== parseInt(i))
        if(parseInt(type.value) !== parseInt(i)){
          variants.value[i].active = false;
        }
        else{
          variants.value[i].active = true;
        }
      }
    }

    onMounted(() => {
      const id = route.params.id;
      selectType(type.value)
    })

    // console.log("Current",current);
    return {
      counters, current, getForm,
      loading, selectType, submitForm, type, variants 
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
    },
    type: {
      deep: true,
      handler: function (newValue, oldValue) {
        // If name or page updates, then we will be able to see it in our
        // newValue variable
        console.log("Watcher: type", newValue, oldValue)
      }
    },
    getForm: {
      deep: true,
      handler: function (newValue, oldValue) {
        // If name or page updates, then we will be able to see it in our
        // newValue variable
        console.log("Watcher: getForm", newValue, oldValue)
      }
    }
  }
});
</script>
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
}

header a {
  font-size: 1.5em;
  padding-left: 50px;
}

div.content {
  clear: both;
}

.left {
  text-align: left;
  padding: 5px 10px;
}

.right {
  text-align: right;
  padding: 5px 10px;
}

input[type=number] {
  width: 100px;
  padding: 3px 5px;
}

.submit-div {
  margin-top: 10px;
  padding-top: 10px;
  border-top: 1px solid #aaa;
  text-align: right;
}

.counter_types {
  display: flex;
  padding: 5px;
}

.counter_types>div {
  padding: 0 10px;
}

.counter_types>div.active {
  font-weight: 700;
}

table {
  width: 100%;
}

th {
  border-bottom: 1px solid #aaa;
}

tr:first-of-type td {
  padding-top: 10px;
}

tbody td:nth-last-of-type(2) {
  text-align: right;
}

form {
  border: 1px solid #999;
  padding: 10px;
}
</style>

<script setup>
</script>

<template>
  <div>
    <header>
      <h2 v-if="counter">
        {{ counter.name }}
      </h2>
    </header>
    <div class="counter-view" v-if="!loading && counter">

      <SortableTable :rows="getReadings"
        :head='[{ title: null, links: [{ title: "bearbeiten", route: "reading", index: "id" }] }, { title: "Datum", column: "date" }, { title: "Zählerstand", column: "reading", class: "right" }]'>
      </SortableTable>

    </div>
    <div v-if="loading" class="spinner-border spinner-border-sm">Lade Daten</div>
  </div>
</template>
<script>
import { RouterLink, useRoute } from 'vue-router';
import { computed, defineComponent, onMounted, ref } from "vue";
import { useCounterStore } from "@/stores/counter";
import { formatDate } from "@/modules/tools";
import SortableTable from "@/components/tools/SortableTable.vue";

export default defineComponent({
  components: { RouterLink, SortableTable },
  setup: () => {
    const route = useRoute();
    const counterStore = useCounterStore();
    const counter = computed(() => {
      let counter = getCounter(route.params.id);
      return counter;
    });
    const editEntry = (id) => {
      console.log("editEntry,id");
    }
    const getReadings = computed(() => {
      let readings = [];
      let reading = {};
      console.log(counter.value.readings);
      for (let i = 0; i < counter.value.readings.length; i++) {
        reading = { ...counter.value.readings[i] };
        // console.log(readings);
        reading["reading"] = (reading["reading"] * 1).toLocaleString(undefined, {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2,
        });
        reading["date"] = formatDate(reading["date"], "d.m.Y");
        readings.push(reading)
      }
      console.log(readings);
      return readings;
    })
    const loading = computed(() => counterStore.loading)

    function getCounter(id) {
      let counter = null;

      if (counterStore?.counters?.[id]) {
        counter = counterStore?.counters?.[id];
        counter.readings = counterStore?.readings?.counters?.[id];
      }
      return counter
    }

    onMounted(() => {
      const id = route.params.id
    })

    return { counter, loading, getReadings, editEntry }
  },
  watch: {
    counter: {
      deep: true,
      handler: function (newValue, oldValue) {
        // If name or page updates, then we will be able to see it in our
        // newValue variable
        console.log("Watcher: counter", newValue, oldValue)
      }
    }
  }
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
</style>
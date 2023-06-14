<script setup>
import SortableTable from "@/components/tools/SortableTable.vue";
</script>

<template>
  <div>
    <header>
      <h2 v-if="counter">
        {{ counter.name }}
      </h2>
    </header>
    <div v-if="!loading && counter">
      <SortableTable :rows="counter.readings" />
    </div>
    <div v-if="loading" class="spinner-border spinner-border-sm">Lade Daten</div>
  </div>
</template>
<script>
import { RouterLink, useRoute } from 'vue-router';
import { computed, defineComponent, onMounted, ref } from "vue";
import { useCounterStore } from "@/stores/counter";

export default defineComponent({
  components: [RouterLink, SortableTable],
  setup: () => {
    const route = useRoute();
    const counterStore = useCounterStore();
    const counter = computed(() => {
      let counter = getCounter(route.params.id);
      return counter;
    });
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

    return { counter, loading }
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

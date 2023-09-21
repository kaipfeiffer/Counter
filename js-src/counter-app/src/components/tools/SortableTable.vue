<template>
  <table v-if="headers">
    <thead>
      <tr>
        <th :colspan="getColumnCount">
          <TableNavigation :start="start" :end="end" :total="total" :settings="settings" @set-page="setPage" />
        </th>
      </tr>
      <tr>
      </tr>
      <tr>
        <th v-for="th in headers">
          {{ th.title }}
        </th>
      </tr>
    </thead>
    <tbody v-if="rows">
      <tr v-for="row in getPage">
        <td v-for="td in headers">
          <span v-if="td.column" :class="td.class">
            {{ row[td.column] }}
          </span>
          <span v-if="td.links">
            <RouterLink  v-for="link in td.links"  :to="{ name: link.route, params: { id: row[link.index]}}" >{{ link.title }}</RouterLink> 
          </span>
        </td>
      </tr>
    </tbody>
    <tfoot>
      <tr>
        <th :colspan="getColumnCount">
          <TableNavigation :start="start" :end="end" :total="total" :settings="settings" @set-page="setPage" />
        </th>
      </tr>
    </tfoot>
  </table>
</template>
<script>
import { RouterLink, useRoute } from 'vue-router';
import { computed, defineComponent, onMounted, ref } from "vue";
import TableNavigation from "@/components/tools/TableNavigation.vue";

export default defineComponent({
  name: 'SortableTable',
  components: { RouterLink, TableNavigation },
  props: {
    head: { type: Object },
    settings: { type: Object },
    rows: { required: true, type: Object },
  },
  setup: (props) => {
    const createHeaders = () => {
      let first = props.rows?.length ? props.rows[0] : {}
      let header = [];
      console.log('length', Object.keys(props.rows).length)
      console.log('first', first)
      for (let i in first) {
        header.push({
          title: i,
          column: i
        })
      }
      console.log('header', header)
      return header;
    }
    const defaultSettings = {
      pageSize: 20,
      title: "Einträge %1$s-%2$s von %3$s",
      firstPage: "<<",
      previousPage: "<",
      nextPage: ">",
      lastPage: ">>",
    }
    const getColumnCount = computed(() => {
      let count = props.rows?.length ? props.rows.length : 1;
      return count;
    });
    const headers = props.head ? props.head : createHeaders();
    const getPage = computed(() => {
      let rows = props.rows.slice(
        (currentPage.value - 1) * settings.pageSize,
        (currentPage.value) *
        settings.pageSize)
      console.log((currentPage.value - 1) * settings.pageSize, (currentPage.value) * settings.pageSize)
      // console.log("Rows:", rows)
      return rows;
    })
    const start = computed(() => {
      return props.rows?.length ? (currentPage.value - 1) * settings.pageSize + 1 : 0;
    })

    const total = computed(() => {
      return props.rows?.length;
    })

    const end = computed(() => {
      let last = (currentPage.value * settings.pageSize);
      return props.rows?.length && props.rows?.length > last ? last : props.rows?.length;
    })

    const getTitle = computed(() => {
      let first = start.value; // props.rows?.length ? (currentPage.value - 1) * settings.pageSize + 1 : 0;
      let last = end.value; //(currentPage.value * settings.pageSize);
      last = props.rows?.length && props.rows?.length > last ? last : props.rows?.length;
      let title = settings.title
        .replace('%3$s', total.value
          //props.rows?.length
        )
        .replace('%1$s', first)
        .replace('%2$s', last)
      return title
    })
    const route = useRoute();
    const settings = { ...defaultSettings, ...props.settings }

    const setPage = (index) => {
      switch (index) {
        case 0: {
          currentPage.value = 1;
          break;
        }
        case -1: {
          currentPage.value = currentPage.value > 1 ? currentPage.value - 1 : 1;
          break;
        }
        case 1: {
          currentPage.value = currentPage.value <= (props.rows?.length / settings.pageSize) ? currentPage.value + 1 : Math.floor(props.rows?.length / settings.pageSize) + 1;
          break;
        }
        default: {
          currentPage.value = Math.floor(props.rows?.length / settings.pageSize) + 1;
        }
      }

      console.log("Hallo", index, currentPage.value, props.rows?.length, settings.pageSize);
    }

    const currentPage = ref(1);
    onMounted(() => {
      const id = route.params.id
    })

    console.log('Props', props)
    console.log('Rows', props.rows)
    return { end, getColumnCount, getPage, getTitle, headers, settings, start, total, setPage }
  },
  watch: {
    headers: {
      deep: true,
      handler: function (newValue, oldValue) {
        // If name or page updates, then we will be able to see it in our
        // newValue variable
        console.log("Watcher: headers", newValue, oldValue)
      }
    }
  }
});
</script>
<style scoped>
table {
  width: 100%;
  border: 1px solid var(--color-border);
}

thead th {
  border-bottom: 1px solid var(--color-border);
}

tfoot th:first-of-type {
  border-top: 1px solid var(--color-border);
}

td,
th {
  text-align: center;
}
</style>

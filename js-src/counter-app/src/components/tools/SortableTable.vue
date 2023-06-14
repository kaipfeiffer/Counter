<template>
  <table v-if="headers">
    <thead>
      <tr>
        <th :colspan="getColumnCount"></th>
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
          {{ row[td.column] }}
        </td>
      </tr>
    </tbody>
  </table>
</template>
<script>
import { RouterLink, useRoute } from 'vue-router';
import { computed, defineComponent, onMounted, ref } from "vue";

export default defineComponent({
  name: 'SortableTable',
  components: { RouterLink },
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
        let rows =  props.rows.slice((currentPage - 1) * settings.pageSize,(currentPage ) * settings.pageSize)
        console.log((currentPage - 1) * settings.pageSize,(currentPage ) * settings.pageSize)
        console.log("Rows:",rows)
        return rows;
    })
    const getTitle = computed(() => {
      let title = settings.title.replace('%3$s', props.rows?.length)
                  .replace('%1$s',(currentPage - 1) * settings.pageSize + 1)
                  .replace('%2$s',(currentPage * settings.pageSize))
      return title
    })
    const route = useRoute();
    const settings = { ...defaultSettings, ...props.settings }
    
    let currentPage = 1;
    onMounted(() => {
      const id = route.params.id
    })

    console.log('Props', props)
    console.log('Rows', props.rows)
    return { getColumnCount, headers, getPage }
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

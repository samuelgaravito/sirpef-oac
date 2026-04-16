<script lang="ts" setup>
import { computed, ref, onMounted } from 'vue';
import { use } from 'echarts/core';
import { CanvasRenderer } from 'echarts/renderers';
import { TreeChart, PieChart } from 'echarts/charts';
import {
  TitleComponent,
  TooltipComponent,
  LegendComponent,
  GridComponent
} from 'echarts/components';
import VChart from 'vue-echarts';
import { getTreeviewData } from '@/modules/FeDeVida/services';
import Loader from '@/components/Votos/loader.vue';
import { useBreakpoint } from '@/composables/useBreakpoint';
import { useRouter } from 'vue-router'; 


use([
  CanvasRenderer, TreeChart, PieChart, TitleComponent,
  TooltipComponent, LegendComponent, GridComponent
]);

const props = defineProps<{
  solicitudesData: { 
    id: number, 
    name: string, 
    value: number, 
    color: string 
  }[]
}>();

const emit = defineEmits(['redirectToStatic']);

const { isMobile } = useBreakpoint();
const loading = ref(true);
const loadingTree = ref(true); 
const treeData = ref(null);
const donutData = ref([]);
const rawApiData = ref([]);

const isDrillDownView = ref(false);
const currentParent = ref(null);
const drillDownData = ref([]);
const isTreeExpanded = ref(false);
const allEstatusTramite = ref([]);
const router = useRouter(); 

// --- CONTADOR GLOBAL PARA IDs ÚNICOS ---
let nodeIdCounter = 1000;

const processDataForTree = (apiData) => {
  if (!apiData || apiData.length === 0) {
    return { name: 'No hay datos', value: 0, children: [] };
  }
  const nodes = {};
  let totalGeneral = 0;
  apiData.forEach(caso => {
    nodes[caso.id] = { ...caso, children: [] };
    totalGeneral += caso.total_por_tipo;
  });

  const rootNodes = [];
  apiData.forEach(caso => {
    if (caso.tipo_caso_padre_id && nodes[caso.tipo_caso_padre_id]) {
      nodes[caso.tipo_caso_padre_id].children.push(nodes[caso.id]);
    } else {
      rootNodes.push(nodes[caso.id]);
    }
  });

  const buildEchartsTree = (node, parentColor = '#808080') => {
    const currentColor = node.color || parentColor;
    const hasChildrenCaseTypes = node.children && node.children.length > 0;
    let childrenForEcharts = [];

    if (hasChildrenCaseTypes) {
      childrenForEcharts = node.children.map(childNode => buildEchartsTree(childNode, currentColor));
    } else {
      const createBreakdownChildren = (breakdownArray) => {
        if (!breakdownArray || breakdownArray.length === 0) return [];
        return breakdownArray
          .filter(item => item.value > 0)
          .map(item => ({
            node_id: `substatus_${item.id}_${nodeIdCounter++}`, // ID único para sub-estado
            name: `${item.name}: ${item.value}`,
            value: item.value,
            id: item.id, // ID original de la DB
            isSubStatus: true,
            isTotalStatusNode: false,
            isClickableLeafStatus: false,
            itemStyle: { color: currentColor, borderColor: currentColor },
            lineStyle: { color: currentColor }
        }));
      };

      const statusConfig = [
        { key: 'en_tramite', displayName: 'En Tramite' },
        { key: 'orientados', displayName: 'Orientado' },
        { key: 'resultado_directo', displayName: 'Resultado Directo' },
        { key: 'remitidos', displayName: 'Remitido a Otro' },
        { key: 'cerrados', displayName: 'Cerrado' },
      ];
      
      const statusNodes = [];

      statusConfig.forEach(config => {
        const count = node[config.key]; 
        if (count > 0) {
          const breakdownChildren = createBreakdownChildren(node[`${config.key}_breakdown`]);
          
          if (breakdownChildren.length > 0) {
            breakdownChildren.unshift({
              node_id: `total_${config.key}_${nodeIdCounter++}`, // ID único para "Todos"
              name: `Todos: ${count}`,
              value: count,
              rawStatusName: config.displayName,
              isTotalStatusNode: true,
              isSubStatus: false,
              isClickableLeafStatus: false,
              itemStyle: { color: currentColor, borderColor: currentColor, borderType: 'dashed' },
              lineStyle: { color: currentColor, type: 'dashed' }
            });
          }

          statusNodes.push({
            node_id: `status_${config.key}_${nodeIdCounter++}`, // ID único para estado principal
            name: `${config.displayName}: ${count}`,
            value: count,
            rawStatusName: config.displayName,
            isStatusNode: true, // Bandera para identificar nodos de estado
            children: breakdownChildren,
            isTotalStatusNode: false,
            isSubStatus: false,
            isClickableLeafStatus: breakdownChildren.length === 0,
            itemStyle: { color: currentColor, borderColor: currentColor },
            lineStyle: { color: currentColor }
          });
        }
      });
      childrenForEcharts = statusNodes;
    }

    const nodeValue = hasChildrenCaseTypes
      ? node.children.reduce((sum, child) => sum + child.total_por_tipo, 0)
      : node.total_por_tipo;

    return {
      node_id: `casetype_${node.id}`, // ID único para Tipo de Caso
      name: `${node.tipo}: ${nodeValue}`,
      value: nodeValue,
      id: node.id, // ID original de la DB
      isCaseType: true, // Bandera para identificar nodos de Tipo de Caso
      children: childrenForEcharts,
      itemStyle: { color: currentColor, borderColor: currentColor },
      lineStyle: { color: currentColor }
    };
  };

  const finalChildren = rootNodes.map(rootNode => buildEchartsTree(rootNode));
  
  return {
    node_id: 'root_node', // ID único para el nodo raíz
    name: `Total Casos: ${totalGeneral}`,
    value: totalGeneral,
    children: finalChildren,
  };
};

const treeChartOption = computed(() => {
  if (loading.value || !treeData.value) return {};
  
  return {
    tooltip: { trigger: 'item', triggerOn: 'mousemove', formatter: '{b}' },
    series: [{
      type: 'tree',
      data: [treeData.value],
      orient: isMobile.value ? 'TB' : 'LR',
      top: '5%', left: '15%', bottom: '5%', right: '25%',
      symbolSize: 10,
      edgeShape: 'curve',
      initialTreeDepth: 1,
      label: {
        backgroundColor: '#fff',
        padding: [4, 6],
        borderRadius: 4,
        borderWidth: 1.5,
        borderColor: 'inherit',
        position: isMobile.value ? 'top' : 'left',
        verticalAlign: 'middle',
        align: isMobile.value ? 'center' : 'right',
      },
      leaves: {
        label: {
          backgroundColor: '#fff',
          padding: [4, 6],
          borderRadius: 4,
          borderWidth: 1.5,
          borderColor: 'inherit',
          position: isMobile.value ? 'bottom' : 'right',
          verticalAlign: 'middle',
          align: isMobile.value ? 'center' : 'left',
        }
      },
      emphasis: { focus: 'descendant' },
      expandAndCollapse: true,
    }]
  };
});

const processDataForDonut = (apiData) => {
    if (!apiData || apiData.length === 0) return [];
    const nodes = {};
    apiData.forEach(caso => {
        nodes[caso.id] = { ...caso, children: [] };
    });
    const rootNodes = [];
    apiData.forEach(caso => {
        if (caso.tipo_caso_padre_id && nodes[caso.tipo_caso_padre_id]) {
            nodes[caso.tipo_caso_padre_id].children.push(nodes[caso.id]);
        } else {
            rootNodes.push(nodes[caso.id]);
        }
    });
    const getTotalDescendantCount = (node) => {
        let total = node.total_por_tipo;
        if (node.children && node.children.length > 0) {
            node.children.forEach(child => {
                total += getTotalDescendantCount(nodes[child.id]);
            });
        }
        return total;
    };
    return rootNodes.map(rootNode => {
        const totalValue = getTotalDescendantCount(rootNode);
        return {
            name: rootNode.tipo,
            value: 1,
            realValue: totalValue,
            id: rootNode.id,
            itemStyle: { color: rootNode.color || '#e97a18' }
        };
    }).filter(item => item.realValue > 0);
};

onMounted(async () => {
  loading.value = true;
  loadingTree.value = true;
  try {
    const apiResponseData = await getTreeviewData();
    rawApiData.value = apiResponseData;
    donutData.value = processDataForDonut(apiResponseData);
    treeData.value = processDataForTree(apiResponseData);
  } catch (error) {
    console.error("Error al cargar los datos del gráfico:", error);
    treeData.value = { name: 'Error al cargar datos', value: 0, children: [] };
  } finally {
    loading.value = false;
    loadingTree.value = false;
  }
});

const donutChartOption = computed(() => {
  if (loading.value) return {};
  const dataToRender = isDrillDownView.value ? drillDownData.value : donutData.value;

  const startAngle = isMobile.value ? -250 : 180;
  const endAngle = isMobile.value ? -470 : 360;
  const centerPosition = isMobile.value ? ['15%', '50%'] : ['50%', '75%'];
  return {
    tooltip: { trigger: 'item', formatter: (params) => params.seriesName === 'Inner Labels' ? null : `${params.name}: ${params.data.realValue}` },
    series: [{
      name: 'Tipos de Solicitud', type: 'pie', radius: ['50%', '80%'],
      center: centerPosition, startAngle: startAngle, endAngle: endAngle, avoidLabelOverlap: true,
      label: { show: true, position: 'outside', formatter: '{b}', color: '#FFFFFF', fontSize: isMobile.value ? 10 : 12, },
      labelLine: { show: true, length: isMobile.value ? 5 : 15, length2: 5, },
      data: dataToRender,
    }, {
      name: 'Inner Labels', type: 'pie', radius: ['50%', '80%'],
      center: centerPosition, startAngle: startAngle, endAngle: endAngle, silent: true, itemStyle: { color: 'transparent' },
      labelLine: { show: false },
      label: {
        show: true, position: 'inside', formatter: (params) => params.data.realValue > 0 ? params.data.realValue : '',
        color: '#FFFFFF', fontWeight: 'bold', fontSize: isMobile.value ? 16 : 20,
      },
      data: dataToRender,
    }]
  };
});

const handleDonutClick = (params) => {
  if (isTreeExpanded.value) return;

  if (isDrillDownView.value) {
    emit('redirectToStatic', { id: params.data.id });
    return;
  }
  
  const clickedNode = rawApiData.value.find(item => item.id === params.data.id);
  const children = rawApiData.value.filter(item => item.tipo_caso_padre_id === clickedNode.id);

  if (children.length > 0) {
    isDrillDownView.value = true;
    currentParent.value = clickedNode;
    drillDownData.value = children.filter(item => item.total_por_tipo > 0).map(item => ({
        name: item.tipo,
        value: 1,
        realValue: item.total_por_tipo,
        id: item.id,
        itemStyle: { color: item.color || '#e97a18' }
    }));
  } else {
    emit('redirectToStatic', { id: clickedNode.id });
  }
};

const backToMainView = () => {
    isDrillDownView.value = false;
    currentParent.value = null;
    drillDownData.value = [];
};

const expandTree = () => { isTreeExpanded.value = true; };
const closeTree = () => { isTreeExpanded.value = false; };

const findNodePath = (node, targetNodeId, path = []) => {
    if (node.node_id === targetNodeId) {
        return path;
    }
    if (node.children) {
        for (const child of node.children) {
            const foundPath = findNodePath(child, targetNodeId, [...path, node]);
            if (foundPath) return foundPath;
        }
    }
    return null;
};

const handleTreeClick = (params) => {
    if (!params || !params.data || !params.data.node_id || !treeData.value) return;

    const clickedNode = params.data;
    const isRootNode = clickedNode.node_id === treeData.value.node_id;

    if (isRootNode) {
        setTimeout(() => closeTree(), 300);
        return;
    }

    const isActionNode = clickedNode.isSubStatus || clickedNode.isTotalStatusNode || clickedNode.isClickableLeafStatus;

    if (!isActionNode) {
        return;
    }

    const pathInfo = findNodePath(treeData.value, clickedNode.node_id);
    if (!pathInfo) return;

    const reversedPath = [...pathInfo].reverse();

    const tipoCasoNode = reversedPath.find(node => node.isCaseType === true);
    if (!tipoCasoNode) return;

    const query = {
        tipo_caso_id: tipoCasoNode.id
    };

    if (clickedNode.isSubStatus) {
        const statusNode = reversedPath.find(node => node.isStatusNode === true);
        if (statusNode) {
            query.estatus_caso = statusNode.rawStatusName;
            query.estatus_tramite_id = clickedNode.id;
        }
    } else if (clickedNode.isTotalStatusNode || clickedNode.isClickableLeafStatus) {
        query.estatus_caso = clickedNode.rawStatusName;
    }

    router.push({
        path: '/cases',
        query
    });
};

</script>

<template>
  <div class="chart-container">
    <div v-if="loading" class="flex justify-center items-center h-full">
      <Loader />
    </div>
    <template v-else>
      
      <button
        v-if="treeData && !loadingTree && !isDrillDownView" 
        @click="expandTree"
        class="tree-expander-button"
        :class="{ 'chart-hidden': isTreeExpanded }"
        title="Ver desglose de casos"
      >
        <font-awesome-icon icon="fa-solid fa-sitemap" class="text-xl"/>
        <span class="total-cases-number">{{ treeData.value }}</span>
        <span class="total-cases-label">Total Casos</span>
      </button>

       <button
        v-if="isDrillDownView"
        @click="backToMainView"
        class="tree-expander-button"
        title="Volver a la vista principal"
      >
        <font-awesome-icon icon="fa-solid fa-arrow-left" class="text-2xl"/>
        <span class="total-cases-label mt-2">Volver</span>
      </button>

      <VChart
        class="chart"
        :class="{ 'chart-hidden': isTreeExpanded }"
        :option="donutChartOption"
        @click="handleDonutClick"
        autoresize
      />

      <div class="tree-wrapper" :class="{ 'expanded': isTreeExpanded }">
        <button @click="closeTree" class="close-button" title="Cerrar vista de árbol">×</button>
        <VChart
            v-if="isTreeExpanded && !loadingTree"
            class="chart"
            :option="treeChartOption"
            autoresize
            @click="handleTreeClick" 
        />
        <div v-if="isTreeExpanded && loadingTree" class="flex justify-center items-center h-full">
          <Loader />
        </div>
      </div>
    </template>
  </div>
</template>

 <style scoped>
.chart-container {
  position: relative;
  width: 100%;
  height: 100%;
  min-height: 450px;
}
.drilldown-header {
  position: absolute;
  top: 10px;
  left: 10px;
  z-index: 15;
  display: flex;
  align-items: center;
  gap: 15px;
}
.back-button {
  background-color: rgba(255,255,255,0.8);
  border: 1px solid #ccc;
  padding: 5px 10px;
  border-radius: 5px;
  cursor: pointer;
  font-size: 14px;
  color: #333;
}
.back-button:hover {
  background-color: #f0f0f0;
}
.drilldown-title {
  font-size: 16px;
  font-weight: bold;
  color: #fff;
}
.chart {
  width: 100%;
  height: 100%;
}
.tree-wrapper {
  position: absolute;
  top: 0;
  left:0;
  width: 100%;
  height: 100%;
  background-color: transparent;
  backdrop-filter: blur(5px);
  border-radius: 1rem;
  z-index: 20;
  opacity: 0;
  visibility: hidden;
  transform: scale(0.9);
  transition: opacity 0.4s ease, visibility 0.4s ease, transform 0.4s ease;
}
.tree-wrapper.expanded {
  opacity: 1;
  visibility: visible;
  transform: scale(1);
}
.tree-expander-button {
  position: absolute;
  z-index: 10;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  width: 110px;
  height: 110px;
  background-color: #fff;
  color: #041e42;
  border: 3px solid #041e42;
  border-radius: 50%;
  cursor: pointer;
  font-weight: bold;
  transition: all 0.3s ease;
  padding: 10px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  top: 70%;
  left: 50%;
  transform: translate(-50%, -50%);
}
.tree-expander-button:hover {
  transform: translate(-50%, -50%) scale(1.05);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
}
@media (max-width: 767px) {
  .tree-expander-button { top: 50%; left: 15%; }
  .tree-expander-button:hover { transform: translate(-50%, -50%) scale(1.05); }
}
.total-cases-number {
  font-size: 2rem;
  line-height: 1;
}
.total-cases-label {
  font-size: 0.8rem;
  margin-top: 4px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.chart-hidden {
  opacity: 0;
  visibility: hidden;
  pointer-events: none;
}
.close-button {
  position: absolute;
  top: 15px;
  right: 20px;
  background: rgba(255, 255, 255, 0.2);
  color: white;
  border: 1px solid white;
  border-radius: 50%;
  width: 30px;
  height: 30px;
  font-size: 24px;
  line-height: 28px;
  text-align: center;
  cursor: pointer;
  z-index: 15;
  transition: background-color 0.3s, transform 0.3s;
}
.close-button:hover {
  background-color: rgba(255, 255, 255, 0.4);
  transform: scale(1.1);
}
</style>
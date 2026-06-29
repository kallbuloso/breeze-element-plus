// export { default } from 'element-plus/es/locale/lang/pt-br'
/**
 * Element Plus — locale pt-BR
 *
 * Tradução local baseada em element-plus v2.14.1 (dist/locale/pt-br.js).
 * Edite os valores entre aspas que ainda estão em inglês.
 * Não altere as chaves (lado esquerdo de cada par).
 */
export default {
  name: 'pt-br',
  el: {
    breadcrumb: {
      label: 'Breadcrumb' // "Breadcrumb"
    },
    colorpicker: {
      confirm: 'Confirmar',
      clear: 'Limpar',
      defaultLabel: 'seletor de cor', // "color picker"
      description: 'a cor atual é {color}. pressione enter para selecionar uma nova cor.', // "current color is {color}. press enter to select a new color."
      alphaLabel: 'selecione o valor alfa', // "pick alpha value"
      alphaDescription: 'alfa {alpha}, a cor atual é {color}', // "alpha {alpha}, current color is {color}"
      hueLabel: 'selecione o valor de matiz', // "pick hue value"
      hueDescription: 'matiz {hue}, a cor atual é {color}', // "hue {hue}, current color is {color}"
      svLabel: 'selecione saturação e brilho', // "pick saturation and brightness value"
      svDescription: 'saturação {saturation}, brilho {brightness}, a cor atual é {color}', // "saturation {saturation}, brightness {brightness}, current color is {color}"
      predefineDescription: 'selecionar {value} como cor' // "select {value} as the color"
    },
    datepicker: {
      now: 'Agora',
      today: 'Hoje',
      cancel: 'Cancelar',
      clear: 'Limpar',
      confirm: 'Confirmar',
      dateTablePrompt: 'Use as teclas de seta e enter para selecionar o dia do mês', // "Use the arrow keys and enter to select the day of the month"
      monthTablePrompt: 'Use as teclas de seta e enter para selecionar o mês', // "Use the arrow keys and enter to select the month"
      yearTablePrompt: 'Use as teclas de seta e enter para selecionar o ano', // "Use the arrow keys and enter to select the year"
      selectedDate: 'Data selecionada', // "Selected date"
      selectDate: 'Selecione a data',
      selectTime: 'Selecione a hora',
      startDate: 'Data inicial',
      startTime: 'Hora inicial',
      endDate: 'Data final',
      endTime: 'Hora final',
      prevYear: 'Ano anterior',
      nextYear: 'Próximo ano',
      prevMonth: 'Mês anterior',
      nextMonth: 'Próximo mês',
      year: 'Ano',
      month1: 'Janeiro',
      month2: 'Fevereiro',
      month3: 'Março',
      month4: 'Abril',
      month5: 'Maio',
      month6: 'Junho',
      month7: 'Julho',
      month8: 'Agosto',
      month9: 'Setembro',
      month10: 'Outubro',
      month11: 'Novembro',
      month12: 'Dezembro',
      weeks: {
        sun: 'Dom',
        mon: 'Seg',
        tue: 'Ter',
        wed: 'Qua',
        thu: 'Qui',
        fri: 'Sex',
        sat: 'Sáb' // "Sab" → com acento
      },
      weeksFull: {
        sun: 'Domingo', // "Sunday"
        mon: 'Segunda-feira', // "Monday"
        tue: 'Terça-feira', // "Tuesday"
        wed: 'Quarta-feira', // "Wednesday"
        thu: 'Quinta-feira', // "Thursday"
        fri: 'Sexta-feira', // "Friday"
        sat: 'Sábado' // "Saturday"
      },
      months: {
        jan: 'Jan',
        feb: 'Fev',
        mar: 'Mar',
        apr: 'Abr',
        may: 'Mai',
        jun: 'Jun',
        jul: 'Jul',
        aug: 'Ago',
        sep: 'Set',
        oct: 'Out',
        nov: 'Nov',
        dec: 'Dez'
      }
    },
    inputNumber: {
      decrease: 'diminuir número', // "decrease number"
      increase: 'aumentar número' // "increase number"
    },
    select: {
      loading: 'Carregando',
      noMatch: 'Sem resultados',
      noData: 'Sem dados',
      placeholder: 'Selecione'
    },
    mention: {
      loading: 'Carregando'
    },
    dropdown: {
      toggleDropdown: 'Alternar menu' // "Toggle Dropdown"
    },
    cascader: {
      noMatch: 'Sem resultados',
      loading: 'Carregando',
      placeholder: 'Selecione',
      noData: 'Sem dados'
    },
    pagination: {
      goto: 'Ir para',
      pagesize: '/página',
      total: 'Total {total}',
      pageClassifier: '',
      page: 'Página', // "Page"
      prev: 'Ir para a página anterior', // "Go to previous page"
      next: 'Ir para a próxima página', // "Go to next page"
      currentPage: 'página {pager}', // "page {pager}"
      prevPages: '{pager} páginas anteriores', // "Previous {pager} pages"
      nextPages: 'Próximas {pager} páginas', // "Next {pager} pages"
      deprecationWarning: 'Uso depreciado detectado, consulte a documentação do el-pagination para mais detalhes' // "Deprecated usages detected..."
    },
    dialog: {
      close: 'Fechar este diálogo' // "Close this dialog"
    },
    drawer: {
      close: 'Fechar este painel' // "Close this dialog"
    },
    messagebox: {
      title: 'Mensagem',
      confirm: 'Confirmar',
      cancel: 'Cancelar',
      error: 'Erro!',
      close: 'Fechar este diálogo' // "Close this dialog"
    },
    upload: {
      deleteTip: 'pressione delete para apagar', // "aperte delete para apagar"
      delete: 'Apagar',
      preview: 'Pré-visualizar',
      continue: 'Continuar'
    },
    slider: {
      defaultLabel: 'controle deslizante entre {min} e {max}', // "slider between {min} and {max}"
      defaultRangeStartLabel: 'selecione o valor inicial', // "pick start value"
      defaultRangeEndLabel: 'selecione o valor final' // "pick end value"
    },
    table: {
      emptyText: 'Sem dados',
      confirmFilter: 'Confirmar',
      resetFilter: 'Limpar',
      clearFilter: 'Todos',
      sumText: 'Total',
      selectAllLabel: 'Selecionar todas as linhas', // "Select all rows"
      selectRowLabel: 'Selecionar esta linha', // "Select this row"
      expandRowLabel: 'Expandir esta linha', // "Expand this row"
      collapseRowLabel: 'Recolher esta linha', // "Collapse this row"
      sortLabel: 'Ordenar por {column}', // "Sort by {column}"
      filterLabel: 'Filtrar por {column}' // "Filter by {column}"
    },
    tag: {
      close: 'Fechar esta tag' // "Close this tag"
    },
    tour: {
      next: 'Próximo',
      previous: 'Anterior',
      finish: 'Finalizar',
      close: 'Fechar' // "Close this dialog"
    },
    tree: {
      emptyText: 'Sem dados'
    },
    transfer: {
      noMatch: 'Sem resultados',
      noData: 'Sem dados',
      titles: ['Lista 1', 'Lista 2'],
      filterPlaceholder: 'Digite uma palavra-chave',
      noCheckedFormat: '{total} itens',
      hasCheckedFormat: '{checked}/{total} selecionados'
    },
    image: {
      error: 'Erro ao carregar imagem'
    },
    pageHeader: {
      title: 'Voltar'
    },
    popconfirm: {
      confirmButtonText: 'Sim',
      cancelButtonText: 'Não'
    },
    carousel: {
      leftArrow: 'Seta esquerda do carrossel', // "Carousel arrow left"
      rightArrow: 'Seta direita do carrossel', // "Carousel arrow right"
      indicator: 'Carrossel mudar para índice {index}' // "Carousel switch to index {index}"
    },
    inputOTP: {
      groupLabel: 'Entrada OTP', // to be translated
      defaultLabel: 'Por favor, insira o caractere OTP {index}' // to be translated
    }
  }
}

export default {
    name: "pt",
    el: {
        breadcrumb: { label: "Navegação estrutural" },
        colorpicker: {
            confirm: "Confirmar",
            clear: "Limpar",
            defaultLabel: "seletor de cores",
            description: "a cor atual é {color}. prima Enter para selecionar uma nova cor.",
            alphaLabel: "selecionar o valor alfa",
            alphaDescription: "alfa {alpha}, a cor atual é {color}",
            hueLabel: "selecionar o valor de matiz",
            hueDescription: "matiz {hue}, a cor atual é {color}",
            svLabel: "selecionar os valores de saturação e brilho",
            svDescription: "saturação {saturation}, brilho {brightness}, a cor atual é {color}",
            predefineDescription: "selecionar {value} como cor"
        },
        datepicker: {
            now: "Agora",
            today: "Hoje",
            cancel: "Cancelar",
            clear: "Limpar",
            confirm: "Confirmar",
            dateTablePrompt: "Utilize as teclas de seta e Enter para selecionar o dia do mês",
            monthTablePrompt: "Utilize as teclas de seta e Enter para selecionar o mês",
            yearTablePrompt: "Utilize as teclas de seta e Enter para selecionar o ano",
            selectedDate: "Data selecionada",
            selectDate: "Selecionar data",
            selectTime: "Selecionar hora",
            startDate: "Data de início",
            startTime: "Hora de início",
            endDate: "Data de fim",
            endTime: "Hora de fim",
            prevYear: "Ano anterior",
            nextYear: "Ano seguinte",
            prevMonth: "Mês anterior",
            nextMonth: "Mês seguinte",
            year: "Ano",
            month1: "Janeiro",
            month2: "Fevereiro",
            month3: "Março",
            month4: "Abril",
            month5: "Maio",
            month6: "Junho",
            month7: "Julho",
            month8: "Agosto",
            month9: "Setembro",
            month10: "Outubro",
            month11: "Novembro",
            month12: "Dezembro",
            weeks: {
                sun: "Dom",
                mon: "Seg",
                tue: "Ter",
                wed: "Qua",
                thu: "Qui",
                fri: "Sex",
                sat: "Sáb"
            },
            weeksFull: {
                sun: "Domingo",
                mon: "Segunda-feira",
                tue: "Terça-feira",
                wed: "Quarta-feira",
                thu: "Quinta-feira",
                fri: "Sexta-feira",
                sat: "Sábado"
            },
            months: {
                jan: "Jan",
                feb: "Fev",
                mar: "Mar",
                apr: "Abr",
                may: "Mai",
                jun: "Jun",
                jul: "Jul",
                aug: "Ago",
                sep: "Set",
                oct: "Out",
                nov: "Nov",
                dec: "Dez"
            }
        },
        inputNumber: {
            decrease: "diminuir número",
            increase: "aumentar número"
        },
        select: {
            loading: "A carregar",
            noMatch: "Sem correspondência",
            noData: "Sem dados",
            placeholder: "Selecione"
        },
        mention: { loading: "A carregar" },
        dropdown: { toggleDropdown: "Alternar menu pendente" },
        cascader: {
            noMatch: "Sem correspondência",
            loading: "A carregar",
            placeholder: "Selecione",
            noData: "Sem dados"
        },
        pagination: {
            goto: "Ir para",
            pagesize: "/página",
            total: "Total {total}",
            pageClassifier: "",
            page: "Página",
            prev: "Ir para a página anterior",
            next: "Ir para a página seguinte",
            currentPage: "página {pager}",
            prevPages: "{pager} páginas anteriores",
            nextPages: "{pager} páginas seguintes",
            deprecationWarning: "Foram detetadas utilizações obsoletas; consulte a documentação do el-pagination para obter mais detalhes"
        },
        dialog: { close: "Fechar esta caixa de diálogo" },
        drawer: { close: "Fechar este painel" },
        messagebox: {
            title: "Mensagem",
            confirm: "Confirmar",
            cancel: "Cancelar",
            error: "Erro!",
            close: "Fechar esta caixa de diálogo"
        },
        upload: {
            deleteTip: "prima Delete para remover",
            delete: "Apagar",
            preview: "Previsualizar",
            continue: "Continuar"
        },
        slider: {
            defaultLabel: "controlo deslizante entre {min} e {max}",
            defaultRangeStartLabel: "selecionar o valor inicial",
            defaultRangeEndLabel: "selecionar o valor final"
        },
        table: {
            emptyText: "Sem dados",
            confirmFilter: "Confirmar",
            resetFilter: "Limpar",
            clearFilter: "Todos",
            sumText: "Total",
            selectAllLabel: "Selecionar todas as linhas",
            selectRowLabel: "Selecionar esta linha",
            expandRowLabel: "Expandir esta linha",
            collapseRowLabel: "Recolher esta linha",
            sortLabel: "Ordenar por {column}",
            filterLabel: "Filtrar por {column}"
        },
        tag: { close: "Fechar esta etiqueta" },
        tour: {
            next: "Seguinte",
            previous: "Anterior",
            finish: "Concluir",
            close: "Fechar"
        },
        tree: { emptyText: "Sem dados" },
        transfer: {
            noMatch: "Sem correspondência",
            noData: "Sem dados",
            titles: ["Lista 1", "Lista 2"],
            filterPlaceholder: "Introduza uma palavra-chave",
            noCheckedFormat: "{total} itens",
            hasCheckedFormat: "{checked}/{total} selecionados"
        },
        image: { error: "Falha ao carregar a imagem" },
        pageHeader: { title: "Voltar" },
        popconfirm: {
            confirmButtonText: "Sim",
            cancelButtonText: "Não"
        },
        carousel: {
            leftArrow: "Seta esquerda do carrossel",
            rightArrow: "Seta direita do carrossel",
            indicator: "Mudar o carrossel para o índice {index}"
        },
        inputOTP: {
            groupLabel: "Entrada OTP",
            defaultLabel: "Introduza o caráter OTP {index}"
        }
    }
}

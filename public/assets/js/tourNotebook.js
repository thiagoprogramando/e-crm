/**
 * Tour
 */

'use strict';

(function () {
  const startBtn = document.querySelector('#shepherd-example');

  function setupTour(tour) {
    const backBtnClass = 'btn btn-sm btn-outline-secondary md-btn-flat waves-effect', nextBtnClass = 'btn btn-sm btn-success btn-next waves-effect waves-light';
    tour.addStep({
      title: 'Criando um Caderno de Questões',
      text: 'Vamos Aprender a Criar o seu Primeiro Caderno de Questões!',
      attachTo: { element: '.dual-listbox', on: 'bottom' },
      buttons: [
        {
          action: tour.cancel,
          classes: backBtnClass,
          text: 'Fechar'
        },
        {
          text: 'Continuar',
          classes: nextBtnClass,
          action: tour.next
        }
      ]
    });
    tour.addStep({
      title: 'Conteúdo e Tópicos',
      text: 'Primeiro, selecione o Conteúdo (Texto em Negrito) que deseja incluir, agora basta fazer um duplo click no Conteúdo para expandir e ver os Tópicos.',
      attachTo: { element: '#available-topics', on: 'top' },
      buttons: [
        {
          text: 'Fechar',
          classes: backBtnClass,
          action: tour.cancel
        },
        {
          text: 'Voltar',
          classes: backBtnClass,
          action: tour.back
        },
        {
          text: 'Continuar',
          classes: nextBtnClass,
          action: tour.next
        }
      ]
    });
    tour.addStep({
      title: 'Selecionando Tópicos',
      text: 'Você pode seleconar Tópicos individuais ou selecionar o Conteúdo inteiro, basta fazer um DUPLO CLICK no Tópico desejado, ou usar a seta Verde!',
      attachTo: { element: '.listbox-controls', on: 'top' },
      buttons: [
        {
          text: 'Fechar',
          classes: backBtnClass,
          action: tour.cancel
        },
        {
          text: 'Voltar',
          classes: backBtnClass,
          action: tour.back
        },
        {
          text: 'Continuar',
          classes: nextBtnClass,
          action: tour.next
        }
      ]
    });
    tour.addStep({
      title: 'Removendo Tópicos',
      text: 'Também é possível remover Tópicos ou Conteúdos inteiros, basta fazer um DUPLO CLICK no Tópico desejado, ou usar a seta Vermelha!',
      attachTo: { element: '.listbox-controls', on: 'top' },
      buttons: [
        {
          text: 'Fechar',
          classes: backBtnClass,
          action: tour.cancel
        },
        {
          text: 'Voltar',
          classes: backBtnClass,
          action: tour.back
        },
        {
          text: 'Continuar',
          classes: nextBtnClass,
          action: tour.next
        }
      ]
    });
    tour.addStep({
      title: 'Filtros',
      text: 'Agora, você pode filtrar as Questões eliminado, limitando ou atribuindo!',
      attachTo: { element: '.filters-section', on: 'top' },
      buttons: [
        {
          text: 'Fechar',
          classes: backBtnClass,
          action: tour.cancel
        },
        {
          text: 'Voltar',
          classes: backBtnClass,
          action: tour.back
        },
        {
          text: 'Continuar',
          classes: nextBtnClass,
          action: tour.next
        }
      ]
    });
    tour.addStep({
      title: 'Revisão Final',
      text: 'Por último, você deve escolher a quantidade de questões que serão atribuidas ao Caderno e um Título!',
      attachTo: { element: '.end-section', on: 'top' },
      buttons: [
        {
          text: 'Fechar',
          classes: backBtnClass,
          action: tour.cancel
        },
        {
          text: 'Voltar',
          classes: backBtnClass,
          action: tour.back
        },
        {
          text: 'Fim',
          classes: nextBtnClass,
          action: tour.cancel
        }
      ]
    });

    return tour;
  }

  if (startBtn) {
    // On start tour button click
    startBtn.onclick = function () {
      const tourVar = new Shepherd.Tour({
        defaultStepOptions: {
          scrollTo: false,
          cancelIcon: {
            enabled: true
          }
        },
        useModalOverlay: true
      });

      setupTour(tourVar).start();
    };
  }

  // ! Documentation Tour only
  const startBtnDocs = document.querySelector('#shepherd-docs-example');

  function setupTourDocs(tour) {
    const backBtnClass = 'btn btn-sm btn-label-secondary md-btn-flat waves-effect',
      nextBtnClass = 'btn btn-sm btn-primary btn-next waves-effect waves-light';
    tour.addStep({
      title: 'Navbar',
      text: 'This is your navbar',
      attachTo: { element: '.navbar', on: 'bottom' },
      buttons: [
        {
          action: tour.cancel,
          classes: backBtnClass,
          text: 'Skip'
        },
        {
          text: 'Next',
          classes: nextBtnClass,
          action: tour.next
        }
      ]
    });
    tour.addStep({
      title: 'Footer',
      text: 'This is the Footer',
      attachTo: { element: '.footer', on: 'top' },
      buttons: [
        {
          text: 'Skip',
          classes: backBtnClass,
          action: tour.cancel
        },
        {
          text: 'Back',
          classes: backBtnClass,
          action: tour.back
        },
        {
          text: 'Next',
          classes: nextBtnClass,
          action: tour.next
        }
      ]
    });
    tour.addStep({
      title: 'Social Link',
      text: 'Click here share on social media',
      attachTo: { element: '.footer-link', on: 'top' },
      buttons: [
        {
          text: 'Back',
          classes: backBtnClass,
          action: tour.back
        },
        {
          text: 'Finish',
          classes: nextBtnClass,
          action: tour.cancel
        }
      ]
    });

    return tour;
  }

  if (startBtnDocs) {
    // On start tour button click
    startBtnDocs.onclick = function () {
      const tourDocsVar = new Shepherd.Tour({
        defaultStepOptions: {
          scrollTo: false,
          cancelIcon: {
            enabled: true
          }
        },
        useModalOverlay: true
      });

      setupTourDocs(tourDocsVar).start();
    };
  }
})();

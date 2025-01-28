document.querySelectorAll('.sidebar ul li').forEach(item => {
    item.addEventListener('click', () => {
      document.querySelector('.sidebar ul li.active').classList.remove('active');
      item.classList.add('active');
  
      const sectionId = item.getAttribute('data-section');
      document.querySelector('.content section.visible').classList.remove('visible');
      document.getElementById(sectionId).classList.add('visible');
    });
  });
  
  document.querySelector('.sidebar ul li.active').click();
  





const faqQuestions = document.querySelectorAll('.faq-question');

faqQuestions.forEach(question => {
      question.addEventListener('click', () => {
          const answer = question.nextElementSibling;
          const isVisible = answer.classList.contains('open');
          
          document.querySelectorAll('.faq-answer').forEach(item => {
              item.classList.remove('open');
          });
  
          if (!isVisible) {
              answer.classList.add('open');
          }
      });
});
  
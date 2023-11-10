<?
		$isQuestionpage = strpos($APPLICATION->GetCurUri(), "course.php?") && strpos($APPLICATION->GetCurUri(), "&TEST_ID=");
		$classHidden = "";
		if ($isQuestionpage ) $classHidden = "visually-hidden";
		?>

</div>
  </div>
	</div>
    </main>
    <footer class="mt-auto">
    <div class="container <?= $classHidden?>">
        <div class="row">
        <div class="footer__content">
                <div class="footer__items">
                    <div class="footer__item">
                        <p>Корпоративный портал</p>
                        <p>ООО "Автодор-Инжиниринг"</p>
                    </div>
                    <div class="footer__item">
                        <p>Помощь по сайту</p>
                        <a href="it-info.docx" download="Руководство пользователя портала">Руководство пользователя портала</a>
                    </div>
                    <div class="footer__item">
                        <p>Описание ресурса</p>
                        <a href="https://avtodor-eng.ru/">avtodor-eng.ru</a>
                    </div>
                    <div class="footer__item">
                        <p>Старый портал</p>
                        <a href="https://10.0.18.10/" target="_blank">10.0.18.10</a>
                    </div>                     
                </div>
                <div class="footer__feedback">
                    <p>Обратная связь по работе портала</p>
                    <a href="/structure/avtodor-inzhiniring-ooo/Otdel-informacionnyh-tehnologiy/Urakov-Boris-Leonidovich/">Борис Ураков</a><br/>
                    <a href="/feedback/">Форма обратной связи</a>
                </div>
            </div>
        </div>
    </div>
    </footer>
    
    <script src="<?= SITE_TEMPLATE_PATH; ?>/libs/slick/slick.min.js"></script>
    <script src="<?= SITE_TEMPLATE_PATH; ?>/js/master.js"></script>
    
</body>
</html>
<?php
$languageRedirect = $_SERVER["REQUEST_URI"] ?? "/cinebox/index.php";
?>
    </div> <!-- .wrapper -->

    <footer class="siteFooterCinebox">
        <div class="siteFooterInner">
            <p class="footerTop"><?php echo htmlspecialchars(t("footer.questions")); ?> <a href="#"><?php echo htmlspecialchars(t("footer.contact_us")); ?></a></p>

            <div class="footerLinks">
                <a href="#"><?php echo htmlspecialchars(t("footer.faq")); ?></a>
                <a href="#"><?php echo htmlspecialchars(t("footer.help_centre")); ?></a>
                <a href="#"><?php echo htmlspecialchars(t("footer.account")); ?></a>
                <a href="#"><?php echo htmlspecialchars(t("footer.media_centre")); ?></a>

                <a href="#"><?php echo htmlspecialchars(t("footer.investor_relations")); ?></a>
                <a href="#"><?php echo htmlspecialchars(t("footer.jobs")); ?></a>
                <a href="#"><?php echo htmlspecialchars(t("footer.ways_to_watch")); ?></a>
                <a href="#"><?php echo htmlspecialchars(t("footer.terms")); ?></a>

                <a href="#"><?php echo htmlspecialchars(t("footer.privacy")); ?></a>
                <a href="#"><?php echo htmlspecialchars(t("footer.cookie_preferences")); ?></a>
                <a href="#"><?php echo htmlspecialchars(t("footer.corporate_information")); ?></a>
                <a href="#"><?php echo htmlspecialchars(t("footer.contact_us")); ?></a>

                <a href="#"><?php echo htmlspecialchars(t("footer.speed_test")); ?></a>
                <a href="#"><?php echo htmlspecialchars(t("footer.legal_notices")); ?></a>
                <a href="#"><?php echo htmlspecialchars(t("footer.only_on_cinebox")); ?></a>
            </div>

            <div class="footerLangWrap">
                <details class="footerLangMenu">
                    <summary class="footerLangBtn">
                        <i class="fa-solid fa-language"></i>
                        <?php echo htmlspecialchars(getLanguageNativeName()); ?>
                        <i class="fa-solid fa-caret-down"></i>
                    </summary>
                    <div class="footerLangOptions">
                        <a href="setLanguage.php?lang=en&amp;redirect=<?php echo urlencode($languageRedirect); ?>"><?php echo htmlspecialchars(t("language.english")); ?></a>
                        <a href="setLanguage.php?lang=vi&amp;redirect=<?php echo urlencode($languageRedirect); ?>"><?php echo htmlspecialchars(t("language.vietnamese")); ?></a>
                    </div>
                </details>
            </div>

            <p class="footerCountry"><?php echo htmlspecialchars(t("footer.country")); ?></p>
            <p class="footerCaptcha">
                <?php echo htmlspecialchars(t("footer.captcha")); ?>
                <a href="#"><?php echo htmlspecialchars(t("footer.learn_more")); ?></a>
            </p>
        </div>
    </footer>
</body>
</html>

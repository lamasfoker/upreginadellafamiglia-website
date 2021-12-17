<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\ContactType;
use App\Repository\CmsPageRepositoryInterface;
use App\Service\BreadcrumbsGetter;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;

final class Contact extends AbstractController
{
    private const DEFAULT_CONTACT_INFORMATION = 'parish_registry';

    private const SLUG_TO_CONTACT_INFORMATION_MAPPING = [
        'santa-maria-assunta' => 'santa_maria_assunta',
        'regina-pacis' => 'regina_pacis',
    ];

    private BreadcrumbsGetter $breadcrumbsGetter;

    private CmsPageRepositoryInterface $cmsPageRepository;

    private MailerInterface $mailer;

    private TranslatorInterface $translator;

    private string $noReplayMail;

    private string $defaultRecipient;

    public function __construct(
        BreadcrumbsGetter $breadcrumbsGetter,
        CmsPageRepositoryInterface $cmsPageRepository,
        MailerInterface $mailer,
        TranslatorInterface $translator,
        string $noReplayMail,
        string $defaultRecipient
    ) {
        $this->breadcrumbsGetter = $breadcrumbsGetter;
        $this->cmsPageRepository = $cmsPageRepository;
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->noReplayMail = $noReplayMail;
        $this->defaultRecipient = $defaultRecipient;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function index(Request $request): Response
    {
        $form = $this->createForm(ContactType::class);

        $page = (string) $request->get('page');
        if ($page) {
            $form->setData(['page' => $page]);
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $mail = (new TemplatedEmail())
                ->from($this->noReplayMail)
                ->to(new Address($this->getRecipient($data)))
                ->subject($this->translator->trans('app.email.subject'))
                ->htmlTemplate('emails/contact.html.twig')
                ->context(['data' => $data]);

            $this->mailer->send($mail);
            $this->addFlash(
                'notice',
                $this->translator->trans('app.contact_page.flash')
            );

            return $this->redirectToRoute('homepage');
        }

        return $this->render(
            'contact/index.html.twig',
            [
                'breadcrumbs' => $this->breadcrumbsGetter->getContactPageBreadcrumbs(),
                'form' => $form->createView(),
                'contactInformation' => $this->getContactInfo($page)
            ]
        );
    }

    private function getRecipient($formData): string
    {
        $recipient = $this->defaultRecipient;
        if (is_array($formData) && is_string($formData['page'])) {
            $page = $this->cmsPageRepository->getBySlug($formData['page']);
            if ($page) {
                $recipient = $page[CmsPageRepositoryInterface::CONTENTFUL_RESOURCE_REFERENT_MAIL_FIELD_ID];
            }
        }
        return $recipient;
    }

    private function getContactInfo(?string $slug): string
    {
        $contactInfo = self::DEFAULT_CONTACT_INFORMATION;
        if (array_key_exists($slug, self::SLUG_TO_CONTACT_INFORMATION_MAPPING)) {
            $contactInfo = self::SLUG_TO_CONTACT_INFORMATION_MAPPING[$slug];
        }
        return $contactInfo;
    }
}

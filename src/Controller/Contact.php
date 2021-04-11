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
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;

final class Contact extends AbstractController
{
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

    public function index(Request $request): Response
    {
        $form = $this->createForm(ContactType::class);

        $page = $request->get('page');
        if ($page) {
            $form->setData(['page' => $page]);
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mail = $this->defaultRecipient;
            $data = $form->getData();
            if (is_array($data) && is_string($data['page'])) {
                $page = $this->cmsPageRepository->getBySlug($data['page']);
                if ($page) {
                    $mail = $page[CmsPageRepositoryInterface::CONTENTFUL_RESOURCE_REFERENT_MAIL_FIELD_ID];
                }
            }

            $mail = (new TemplatedEmail())
                ->from($this->noReplayMail)
                ->to(new Address($mail))
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
            ['breadcrumbs' => $this->breadcrumbsGetter->getContactPageBreadcrumbs(), 'form' => $form->createView()]
        );
    }
}

<?php

namespace App\Controller;

use App\Form\Type\ThemeUploadType;
use App\Service\ThemeService;
use H2Entwicklung\Signature\CheckSignature;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/room/upload/theme/', name: 'app_upload_theme_')]
class UploadThemeController extends AbstractController
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private ParameterBagInterface $parameterBag,
        private CheckSignature $checkSignature,
      private CacheItemPoolInterface $cacheItemPool,
    )
    {
    }

    #[Route('form', name: 'form', methods: ['GET'])]
    public function index(): Response
    {
        $form = $this->createForm(ThemeUploadType::class, null, ['action' => $this->urlGenerator->generate('app_upload_theme_save')]);
        return $this->render('upload_theme/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('save', name: 'save', methods: ['POST'])]
    public function save(Request $request): Response
    {
        $form = $this->createForm(ThemeUploadType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $themeFile */
            $themeFile = $form->get('theme')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($themeFile) {
                try {

                    $path = $this->parameterBag->get('kernel.project_dir') . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'theme' . DIRECTORY_SEPARATOR . md5(uniqid());
                    $zip = new \ZipArchive();
                    $res = $zip->open($themeFile->getRealPath());
                    if ($res) {
                        $zip->extractTo($path);
                        $zip->close();

                        $finder = new Finder();
                        $finder->files()->in($path)->name('*.json.signed');
                        if ($finder->count() === 1) {
                            $arr = iterator_to_array($finder);
                            $themePath = reset($arr);
                            $theme = $themePath->getContents();
                            $validSignature = $this->checkSignature->verifySignature($theme);
                            if (!$validSignature) {
                                $this->addFlash('danger', 'Theme is invalid');
                                return $this->redirectToRoute('app_upload_theme_form');
                            }
                            $this->moveTheme(reset($arr), $path);
                            $filesystem = new Filesystem();
                            $filesystem->remove($path);
                            $this->cacheItemPool->clear();
                            $this->addFlash('success','Theme successfully uploaded');
                            return $this->redirectToRoute('app_upload_theme_form');
                        } else {
                            $this->addFlash('danger', 'No Theme in the zip');
                            return $this->redirectToRoute('app_upload_theme_form');
                        }

                    }
                }catch (\Exception $exception){
                    $this->addFlash('danger',$exception->getMessage());
                    return $this->redirectToRoute('app_upload_theme_form');
                }
            }else{
                $this->addFlash('danger','No Theme uploaded');
                return $this->redirectToRoute('app_upload_theme_form');
            }
        }
        $this->addFlash('danger','Please upload a zip file');
        return $this->redirectToRoute('app_upload_theme_form');
    }
    public function moveTheme($themePath, $path){
        $filesystem = new Filesystem();
        $tmp = explode(DIRECTORY_SEPARATOR,$themePath);
        $fileName = end($tmp);
        $themeTargetPath = $this->parameterBag->get('kernel.project_dir') . DIRECTORY_SEPARATOR .'theme'.DIRECTORY_SEPARATOR.$fileName;
        $filesystem->remove($themeTargetPath);
        $filesystem->copy($themePath,$themeTargetPath);
        $filesystem->remove($themePath);
        $finder = new Finder();
        $finder->files()->in($path)->directories();
        $arr = iterator_to_array($finder);
        foreach ($arr as $assest){
            $tmp = explode(DIRECTORY_SEPARATOR,$assest);
            $dir = end($tmp);
            $assetTargetPath = $this->parameterBag->get('kernel.project_dir') . DIRECTORY_SEPARATOR .'public'.DIRECTORY_SEPARATOR.$dir;
            $filesystem->remove($assetTargetPath);
            $filesystem->mirror($assest,$assetTargetPath);
        }

    }
}

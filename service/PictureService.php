<?php
/**
 * Created by PhpStorm.
 * User: shurupov
 * Date: 12.03.18
 * Time: 12:44
 */

namespace QEngine\Service;


use PHPImageWorkshop\ImageWorkshop;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class PictureService implements ServiceProviderInterface
{

    const THUMBS_WIDTH = 122;
    const THUMBS_HEIGHT = 91;

    /* @var DataService $dataService */
    private $dataService;

    public function saveImage($id, $path, $sourceUri, $targetSettings, $collection = 'page')
    {

        if (!empty($targetSettings['instances'])) {
            foreach ($targetSettings['instances'] as $key => &$instanceSettings) {
                if (empty($instanceSettings['postfix'])) {
                    $instanceSettings['postfix'] = '-'.$key;
                }
                $uri = $this->adjustImage($sourceUri, $instanceSettings);
                $this->dataService->edit($id, $path . '.' . $key, $uri, $collection);
                $instanceSettings['uri'] = $uri;
            }

            if (!empty($targetSettings['mainInstance']) && $targetSettings['instances'][ $targetSettings['mainInstance'] ]) {
                $this->createThumb(QENGINE_INDEX_PATH. $targetSettings['instances'][ $targetSettings['mainInstance'] ]['uri']);
            } else {
                $this->createThumb(QENGINE_INDEX_PATH. $uri);
            }

            if (!empty($targetSettings['returnInstance']) && $targetSettings['instances'][ $targetSettings['returnInstance'] ]) {
                return $targetSettings['instances'][ $targetSettings['returnInstance'] ]['uri'];
            }
        } else {
            $uri = $this->adjustImage($sourceUri, $targetSettings);
            $this->dataService->edit($id, $path, $uri, $collection);
            $this->createThumb(QENGINE_INDEX_PATH. $uri);
        }

        return $uri;
    }

    private function adjustImage($sourceUri, $targetSettings)
    {
        $targetSettings = array_merge([
            'quality' => 75
        ], $targetSettings);

        $uri = $sourceUri;

        if (!empty($targetSettings['width']) && !empty($targetSettings['height'])) {

            $pathInfo = pathinfo($sourceUri);

            $folder = str_replace('/source', '/source/generated', $pathInfo['dirname']);
            $filename = $pathInfo['filename'].'-'.$targetSettings['width'].'x'.$targetSettings['height'].'.'.$pathInfo['extension'];

            $this->resizeAndCrop(
                QENGINE_INDEX_PATH . $sourceUri,
                QENGINE_INDEX_PATH.$folder, $filename,
                $targetSettings['width'], $targetSettings['height'],
                ( !empty($targetSettings['position']) ? $targetSettings['position'] : 'MM' ),
                $targetSettings['quality']
            );

            $uri = $folder.'/'.$filename;

        }
        elseif (!empty($targetSettings['width']) && empty($targetSettings['height'])) { //Width exists, height doesn't
            $pathInfo = pathinfo($sourceUri);

            $folder = str_replace('/source', '/source/generated', $pathInfo['dirname']);
            $filename = $pathInfo['filename'].'-w'.$targetSettings['width'].'.'.$pathInfo['extension'];

            $this->resize(
                QENGINE_INDEX_PATH . $sourceUri,
                QENGINE_INDEX_PATH.$folder, $filename,
                $targetSettings['width'], null,
                $targetSettings['quality']
            );

            $uri = $folder.'/'.$filename;
        }
        elseif (empty($targetSettings['width']) && !empty($targetSettings['height'])) { //Height exists, width doesn't
            $pathInfo = pathinfo($sourceUri);

            $folder = str_replace('/source', '/source/generated', $pathInfo['dirname']);
            $filename = $pathInfo['filename'].'-h'.$targetSettings['height'].'.'.$pathInfo['extension'];

            $this->resize(
                QENGINE_INDEX_PATH . $sourceUri,
                QENGINE_INDEX_PATH.$folder, $filename,
                null, $targetSettings['height'],
                $targetSettings['quality']
            );

            $uri = $folder.'/'.$filename;
        }

        return $uri;
    }

    private function createThumb($sourcePath)
    {
        $thumbPath = str_replace('/source', '/thumbs', $sourcePath);

        $pathInfo = pathinfo($thumbPath);

        $this->resizeAndCrop(
            $sourcePath,
            $pathInfo['dirname'], $pathInfo['basename'],
            self::THUMBS_WIDTH, self::THUMBS_HEIGHT
        );
    }

    private function resize($sourcePath, $destinationFolder, $destinationFileName, $width, $height, $quality = 75)
    {
        $image = ImageWorkshop::initFromPath($sourcePath);

        $image->resizeInPixel($width, $height, true);

        $image->save($destinationFolder, $destinationFileName, true, true, $quality);
    }

    private function resizeAndCrop($sourcePath, $destinationFolder, $destinationFileName, $width, $height, $position = 'MM', $quality = 75)
    {
        $image = ImageWorkshop::initFromPath($sourcePath);

        $image->cropToAspectRatioInPixel(
            $width,
            $height,
            0, 0, $position
        );

        $image->resizeInPixel($width, $height, true);

        $image->save($destinationFolder, $destinationFileName, true, true, $quality);

    }

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $app A container instance
     */
    public function register(Container $app)
    {
        $this->dataService = $app['dataService'];

        $app['pictureService'] = function () use ($app) {
            return $this;
        };
    }
}
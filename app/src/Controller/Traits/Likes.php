<?php

namespace App\Controller\Traits;

use App\Entity\User;

trait Likes
{
    public function likeVideo($video)
    {
        $user = $this->doctrine->getRepository(User::class)->find($this->getUser());
        $user->addLikedVideo($video);

        $em = $this->doctrine->getManager();
        $em->persist($user);
        $em->flush();

        return 'liked';
    }

    public function dislikeVideo($video)
    {
        $user = $this->doctrine->getRepository(User::class)->find($this->getUser());
        $user->addDislikedVideo($video);

        $em = $this->doctrine->getManager();
        $em->persist($user);
        $em->flush();

        return 'disliked';
    }

    public function undoLikeVideo($video)
    {
        $user = $this->doctrine->getRepository(User::class)->find($this->getUser());
        $user->removeLikedVideo($video);

        $em = $this->doctrine->getManager();
        $em->persist($user);
        $em->flush();

        return 'undo liked';
    }

    public function undoDislikeVideo($video)
    {
        $user = $this->doctrine->getRepository(User::class)->find($this->getUser());
        $user->removeDislikedVideo($video);

        $em = $this->doctrine->getManager();
        $em->persist($user);
        $em->flush();
        return 'undo disliked';
    }
}

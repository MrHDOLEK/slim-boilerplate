<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Application\Dto\Response\UserResponseDto;
use App\Domain\Entity\User\Exception\UserNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

/**
 * @OA\Get(
 *     path="/api/v1/user/{id}",
 *     summary="Get select user by id",
 *     tags={"user"},
 *     @OA\Parameter(
 *          name="id",
 *          required=true,
 *          in="path",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(response = "404", description = "Not Found")
 *)
 *
 * @throws UserNotFoundException
 * @throws HttpBadRequestException
 */
class ViewUserAction extends UserAction
{
    protected function action(): Response
    {
        $userId = (int)$this->resolveArg("id");
        $user = $this->userRepository->findUserOfId($userId);
        return $this->respondWithJson(new UserResponseDto($user));
    }
}

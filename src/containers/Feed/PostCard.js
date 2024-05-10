import React from 'react';
import { View, StyleSheet, TouchableOpacity } from 'react-native';
import {
  Text,
  Card,
  IconButton,
  Avatar,
  Button,
  FullBleed,
  ThreeInARow,
  SixInTwoRows,
  Spotlight,
  NineGrid,
} from 'components';
import { scale, verticalScale } from 'react-native-size-matters';
import { getRandomShop } from 'mocks/shops';
import PropTypes from 'prop-types';

const styles = StyleSheet.create({
  card: {
    flex: 1,
    marginTop: verticalScale(10),
    padding: scale(14),
  },
  footerContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
  },
  row: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  block: {
    paddingVertical: verticalScale(14),
  },
  spacing: {
    paddingTop: verticalScale(14),
  },
  name: {
    paddingHorizontal: scale(14),
    flex: 1,
  },
  icon: {
    marginHorizontal: scale(10),
  },
});

const PostCard = ({
  content, comments, likes, views, images, navigation,
}) => {
  const shop = getRandomShop();

  const renderMedia = () => {
    switch (images.length) {
      case 1:
        return <FullBleed image={images[0]} />;
      case 3:
        return <ThreeInARow images={images} />;
      case 4:
        return <Spotlight images={images} />;
      case 6:
        return <SixInTwoRows images={images} />;
      case 9:
        return <NineGrid images={images} />;
      default:
        return null;
    }
  };

  return (
    <Card style={styles.card}>
      <TouchableOpacity style={styles.row} onPress={() => navigation.navigate('Shop', { id: shop.id })}>
        <Avatar source={shop.image} size={40} />
        <View style={styles.name}>
          <View style={styles.row}>
            <Text flex numberOfLines={1} font="p1" weight="medium">{shop.name}</Text>
          </View>
          <Text color="gray75">{`${shop.followers} followers`}</Text>
        </View>
        <Button
          size="tiny"
          label="Follow"
        />
      </TouchableOpacity>
      <View style={styles.block}>
        <Text color="gray75">{content}</Text>
      </View>
      {renderMedia()}
      <View
        style={[
          styles.footerContainer,
          images[0] && styles.spacing,
        ]}
      >
        <Text color="gray75">{views}</Text>
        <View style={styles.row}>
          <View style={styles.row}>
            <IconButton
              icon="thumbs-up"
              size={14}
              style={styles.icon}
            />
            <Text font="p2" weight="medium">{likes}</Text>
          </View>
          <View style={styles.row}>
            <IconButton
              icon="message-circle"
              size={14}
              style={styles.icon}
              onPress={() => navigation.navigate('FeedComments', { numberOfComments: comments })}
            />
            <Text font="p2" weight="medium">{comments}</Text>
          </View>
        </View>
      </View>
    </Card>
  );
};

PostCard.propTypes = {
  content: PropTypes.string.isRequired,
  likes: PropTypes.number.isRequired,
  comments: PropTypes.number.isRequired,
  views: PropTypes.string.isRequired,
  images: PropTypes.array.isRequired,
  navigation: PropTypes.object.isRequired,
};

export default PostCard;
